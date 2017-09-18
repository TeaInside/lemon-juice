<?php

namespace Handler\Command;

use DB;
use PDO;
use Curl;
use Telegram as B;
use Handler\Command\WhatAnimeCMD;

trait CMDTrait
{
    private function __report($param)
    {
        $a = json_decode(B::getChatAdministrators([
            "chat_id" => $this->chatid
        ], "GET")['content'], true) xor $i = 0;
        if (isset($this->chat['username'])) {
            $group = "<a href=\"https://t.me/".$this->chat['username']."\">".htmlspecialchars($this->chattitle)."</a>";
            $goto = "<b>•</b> <a href=\"https://t.me/".$this->chat['username']."/".$this->msgid."\">Go to the message</a>";
        } else {
            $group = "<b>".htmlspecialchars($this->chattitle)."</b>";
            $goto = "";
        }
        if (!empty($param)) {
            $note = "\n<b>• Note</b>: ".htmlspecialchars($param);
        } else {
            $note = "";
        }
        foreach ($a['result'] as $val) {
            if (strtolower($val['user']['is_bot']) == false) {
                B::sendMessage([
                    "text" => "<b>• Message reported by</b>: <a href=\"tg://user?id=".$this->userid."\">".htmlspecialchars($this->actor)."</a> (<code>".htmlspecialchars($this->userid)."</code>)".($note)."\n<b>• Group</b>: ".$group."\n".$goto,
                    "chat_id" => $val['user']['id'],
                    "parse_mode" => "HTML",
                    "disable_web_page_preview" => "true"
                ])['info']['http_code'] == 200 and ($i++);
            }
        }
        return B::sendMessage([
            "chat_id" => $this->chatid,
            "reply_to_message_id" => $this->msgid,
            "text" => "<i>Reported to {$i} admin(s)</i>",
            "parse_mode" => "HTML"
        ]);
    }

    private function __whatanime()
    {
        if (isset($this->replyto['photo'])) {
            $r = json_decode(B::sendMessage([
                    "text" => "Downloading image...",
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->replyto['message_id']
                ])['content'], true);
            $p = end($this->replyto['photo']);
            $p = json_decode(B::getFile([
                    "file_id" => $p['file_id']
                ])['content'], true);
            $st = new Curl("https://api.telegram.org/file/bot".TOKEN."/".$p['result']['file_path']);
            $st = new WhatAnimeCMD($st->exec());
            B::editMessageText(
                [
                    "text" => "I've got your image.\n\nSearching...",
                    "chat_id" => $this->chatid,
                    "message_id" => $r['result']['message_id'],
                ]
            );
            $st = json_decode($st->exec(), 128);
            if (isset($st['docs'][0])) {
                $a = $st['docs'][0];
                $rep = "Anime yang mirip :\n\n<b>Judul</b> : ".$a['title']."\n";
                isset($a['title_english']) and $rep.="<b>Judul Inggris</b> : ".htmlspecialchars($a['title_english'])."\n";
                isset($a['title_romaji']) and $rep.="<b>Judul Romanji</b> : ".htmlspecialchars($a['title_romaji'])."\n";
                isset($a['episode']) and $rep.= "<b>Episode</b> : ".htmlspecialchars($a['episode'])."\n";
                isset($a['season']) and $rep.= "<b>Season</b> : ".htmlspecialchars($a['season'])."\n";
                isset($a['anime']) and $rep.= "<b>Anime</b> : ".htmlspecialchars($a['anime'])."\n";
                isset($a['file']) and $rep.= "<b>File</b> : ".htmlspecialchars($a['file']);
                B::editMessageText(
                    [
                    "text" => $rep,
                    "parse_mode" => "HTML",
                    "chat_id" => $this->chatid,
                    "message_id" => $r['result']['message_id'],
                    ]
                );
                $video_url = "https://whatanime.ga/".$a['season']."/".$a['anime']."/".$a['file']."?start=".$a['start']."&end=".$a['end']."&token=".$a['token'];
                if (!($video_file = WhatAnimeCMD::check_video($video_url))) {
                    $video_file = WhatAnimeCMD::download_video($video_url);
                }
                $fd = function ($time) {
                    $time = (int)$time;
                    $menit = 0;
                    $detik = 0;
                    while ($time>0) {
                        if ($time>60) {
                            $menit += 1;
                            $time -= 60;
                        } elseif ($time>1) {
                            $detik += $time;
                            $time = 0;
                        }
                    }
                    $menit = (string) $menit;
                    $detik = (string) $detik;
                    return (strlen($menit)==1 ? "0{$menit}" : "{$menit}").":".(strlen($detik)==1 ? "0{$detik}" : "{$detik}");
                };
                B::sendVideo([
                    "video" => "https://webhooks.redangel.ga/whatanime/video/".$video_file,
                    "chat_id" => $this->chatid,
                    "caption" => "Berikut ini adalah cuplikan singkat dari anime yang mirip.\n\nDurasi : ".$fd($a['start'])." - ".$fd($a['end']), $r['result']['message_id'],
                    "reply_to_message_id" => $this->replyto['message_id']
                ]);
            } else {
                B::editMessageText(
                    [
                    "text" => "Mohon maaf, anime yang mirip tidak ditemukan.",
                    "parse_mode" => "HTML",
                    "chat_id" => $this->room_id,
                    "message_id" => $r['result']['message_id'],
                    ]
                );
            }
        } else {
            B::sendMessage("Please reply an image with /whatanime!", $this->room_id, $this->msg_id);
        }
    }

    private function __tg($param)
    {
        $st = DB::prepare("SELECT `text`,`file_id`,`type` FROM `content` WHERE `tag`=:tag AND `chat_id`=:cid LIMIT 1;");
        $exe = $st->execute([
                ":tag" => $param,
                ":cid" => $this->chatid
            ]);
        if (!$exe) {
            var_dump($st->errorInfo());
            die();
        }
        if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
            switch ($st['type']) {
                case 'image/jpg':
                    $arr = [
                            "chat_id" => $this->chatid,
                            "photo" => $st['file_id'],
                            "reply_to_message_id" => (isset($this->replyto) ? $this->replyto['message_id'] : $this->msgid)
                        ];
                    if (!empty($st['text'])) {
                        $arr['caption'] = $st['text'];
                    }
                    return B::sendPhoto($arr);
                    break;
                
                default:
                    # code...
                    break;
            }
        } else {
            B::sendMessage([
                    "text" => "Not found !",
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid
                ]);
        }
    }

    private function __save($param)
    {
        if (isset($this->replyto['photo'])) {
            $tag = explode(" ", $param, 2);
            $a = end($this->replyto['photo']);
            $st = DB::prepare("SELECT `text` FROM `content` WHERE `chat_id`=:cid AND `tag`=:tag LIMIT 1;");
            $exe = $st->execute([
                    ":cid" => $this->chatid,
                    ":tag" => $tag[0]
                ]);
            if (!$exe) {
                var_dump($st->errorInfo());
                die();
            }
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                return B::sendMessage([
                        "text" => "Duplicate content.",
                        "chat_id" => $this->chatid,
                        "reply_to_message_id" => $this->msgid
                    ]);
            } else {
                $rr = json_decode(B::sendMessage([
                        "chat_id" => $this->chatid,
                        "reply_to_message_id" => $this->msgid,
                        "text" => "Downloading image..."
                    ])['content'], true);
                $st = DB::prepare("INSERT INTO `content` (`id`,`chat_id`,`tag`,`text`,`file_id`,`type`,`created_at`) VALUES (null, :cid, :tag, :_text, :file_id, :type, :created_at);");
                $exe = $st->execute([
                        ":cid" => $this->chatid,
                        ":tag" => $tag[0],
                        ":file_id" => $a['file_id'],
                        ":_text" => $tag[1],
                        ":type" => "image/jpg",
                        ":created_at" => date("Y-m-d H:i:s")
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die();
                }
                $w = json_decode(B::getFile([
                    "file_id" => $a['file_id']
                ])['content'], true);
                $ch = new Curl("https://api.telegram.org/file/bot".TOKEN."/".$w['result']['file_path']);
                file_put_contents($n = md5($w['result']['file_path']).".jpg", $ch->exec());
                return B::editMessageText([
                        "message_id" => $rr['result']['message_id'],
                        "text" => "https://webhooks.redangel.ga/".$n,
                        "chat_id" => $this->chatid,
                        "disable_web_page_preview" => true
                    ]);
            }
        }
        return B::sendMessage([
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid,
                    "text" => "nf"
                ]);
    }

    private function __user($param)
    {
    }

    private function __yd($param)
    {
        $wd = explode(" ", $param, 2);
        if (empty($param) || !filter_var($wd[0], FILTER_VALIDATE_URL)) {
            return B::sendMessage(
                [
                    "text" => "Invalid url.",
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid
                ]
            );
        }
        $rr = json_decode(
            B::sendMessage(
                [
                "text" => "Downloading video...",
                "parse_mode" => "HTML",
                "chat_id" => $this->chatid,
                "reply_to_message_id" => $this->msgid
                ]
            )['content'], true
        );
        is_dir(PUBLIC_DIR."/yd") or shell_exec("mkdir -p ".PUBLIC_DIR."/yd");
        is_dir(PUBLIC_DIR."/yd/tmp") or shell_exec("mkdir -p ".PUBLIC_DIR."/yd/tmp");
        $a = shell_exec("cd ".PUBLIC_DIR."/yd/tmp && mkdir ".($tm = time())." && cd \"".$tm."\" && sudo /root/youtube-dl ".$param);
        $file_name = shell_exec("cd ".PUBLIC_DIR."/yd/tmp/".$tm." && ls");
        if (empty($file_name)) {
            return B::editMessageText(
            [
                "text" => "Error.",
                "parse_mode" => "HTML",
                "chat_id" => $this->chatid,
                "message_id" => $rr['result']['message_id'],
                "reply_to_message_id" => $this->msgid,
                "disable_web_page_preview" => true
            ]
        );
        }
        shell_exec("mv ".trim(PUBLIC_DIR."/yd/tmp/".$tm."/".$file_name)." ".PUBLIC_DIR."/yd");
        if (!empty($a)) {
            $a = trim($file_name);
        } else {
            $a = "~";
        }
        shell_exec("rm -rf ".PUBLIC_DIR."/yd/tmp/".$tm);
        B::editMessageText(
            [
                "text" => "https://webhooks.redangel.ga/yd/".$a,
                "parse_mode" => "HTML",
                "chat_id" => $this->chatid,
                "message_id" => $rr['result']['message_id'],
                "reply_to_message_id" => $this->msgid,
                "disable_web_page_preview" => true
            ]
        );
        if (B::sendVideo([
                "caption" => $a,
                "video" => "https://webhooks.redangel.ga/yd/".$a,
                "chat_id" => $this->chatid,
                "reply_to_message_id" => $this->msgid
            ])['info']['http_code'] != 200) {
            return B::sendMessage(
                [
                    "text" => "<b>".$a."</b> reached maximum number of sizes. You can download the video via direct link.",
                    "reply_to_message_id" => $rr['result']['message_id'],
                    "parse_mode" => "HTML",
                    "chat_id" => $this->chatid
                ]
            );
        } else {
            return true;
        }
    }

    private function __sh($param)
    {
        if (strpos($param, "sudo ") !== false) {
            if (in_array($this->userid, SUDOERS)) {
                $a = shell_exec($param." 2>&1");
                $a = empty($a) ? "<pre>~</pre>" : "<pre>".htmlspecialchars($a)."</pre>";
            } else {
                $a = "<a href=\"tg://user?id=".$this->userid."\">".$this->actorcall."</a> is not in the sudoers file. This incident will be reported.";
            }
        } else {
            $a = shell_exec($param." 2>&1");
            $a = empty($a) ? "<pre>~</pre>" : "<pre>".htmlspecialchars($a)."</pre>";
        }
        return B::sendMessage(
            [
                "text" => $a,
                "chat_id" => $this->chatid,
                "reply_to_message_id" => $this->msgid,
                "parse_mode" => "HTML"
            ]
        );
    }

    private function __forgive()
    {
        $flag = false;
        $a = json_decode(
            B::getChatAdministrators(
                [
                "chat_id" => $this->chatid
                ], "GET"
            )['content'], true
        );
        foreach ($a['result'] as $val) {
            if ($val['user']['id'] == $this->userid) {
                if ($val['status']=="creator" || $val['can_restrict_members']) {
                    $flag = true;
                }
                break;
            }
        }
        if ($flag) {
            $uniq = $this->replyto['from']['id']."|".$this->chatid;
            $st = DB::prepare("SELECT `reasons` FROM `user_warning` WHERE `uniq_id`=:uniq LIMIT 1;");
            $exe = $st->execute(
                [
                    ":uniq" => $uniq
                ]
            );
            if (!$exe) {
                var_dump($st->errorInfo());
                die(1);
            }
            $wr = "";
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $wr.= "\n<b>Warns found</b>:\n";
                $st = json_decode($st[0], true) xor $i = 1;
                foreach ($st as $val) {
                    $wr.= ($i++).". ".($val['reason']===null ? "<code>Normal warn</code>" : "<code>".htmlspecialchars($val['reason'])."</code>")."\n";
                }
            }
            $st = DB::prepare("DELETE FROM `user_warning` WHERE `uniq_id`=:uniq LIMIT 1;");
            $exe = $st->execute(
                [
                    ":uniq" => $uniq
                ]
            );
            if (!$exe) {
                var_dump($st->errorInfo());
                die(1);
            }
            return B::sendMessage(
                [
                    "chat_id" => $this->chatid,
                    "text" => "Done! <a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> has been forgiven.".$wr,
                    "parse_mode" => "HTML",
                    "reply_to_message_id" => $this->msgid
                ]
            );
        }
    }

    private function __warn($reason = null)
    {
        $flag = false;
        $a = json_decode(
            B::getChatAdministrators(
                [
                "chat_id" => $this->chatid
                ], "GET"
            )['content'], true
        );
        foreach ($a['result'] as $val) {
            if ($val['user']['id'] == $this->userid) {
                if ($val['status']=="creator" || $val['can_restrict_members']) {
                    $flag = true;
                }
                break;
            }
        }
        if ($flag) {
            $sq = DB::prepare("SELECT `max_warn` FROM `a_known_groups` WHERE `group_id`=:grid LIMIT 1;");
            $exe = $sq->execute(
                [
                    ":grid" => $this->chatid
                ]
            );
            if (!$exe) {
                var_dump($sq->errorInfo());
                die(1);
            }
            $sq = $sq->fetch(PDO::FETCH_NUM);
            $uniq = $this->replyto['from']['id']."|".$this->chatid;
            $st = DB::prepare("SELECT `warn_count`,`reasons` FROM `user_warning` WHERE `uniq_id`=:uniq LIMIT 1;");
            $exe = $st->execute(
                [
                    ":uniq" => $uniq
                ]
            );
            if (!$exe) {
                var_dump($st->errorInfo());
                die(1);
            }
            if ($st = $st->fetch(PDO::FETCH_NUM)) {
                $se = DB::prepare("UPDATE `user_warning` SET `warn_count`=`warn_count`+1,`reasons`=:rr,`updated_at`=:up WHERE `uniq_id`=:uniq LIMIT 1;");
                $st[1] = json_decode($st[1], true) xor $st[0] += 1;
                $st[1][] = ["warned_by"=>$this->userid,"reason"=>$reason,"warned_at"=>date("Y-m-d H:i:s")];
                $exe = $se->execute(
                    [
                        ":rr" => json_encode($st[1]),
                        ":uniq" => $uniq,
                        ":up" => date("Y-m-d H:i:s")
                    ]
                );
                if (!$exe) {
                    var_dump($se->errorInfo());
                    die(1);
                }
                if ($st[0] >= $sq[0]) {
                    $a = B::kickChatMember(
                        [
                            "chat_id" => $this->chatid,
                            "user_id" => $this->replyto['from']['id']
                        ]
                    ) xor $err = "";
                    if ($a['content'] != '{"ok":true,"result":true}') {
                        $err .= json_decode($a['content'], true)['description'];
                    }
                    return B::sendMessage(
                        [
                            "chat_id" => $this->chatid,
                            "text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>banned</b>: reached the max number of warnings (<code>".($st[0])."/".$sq[0]."</code>)\n\n".$err,
                            "parse_mode" => "HTML"
                        ]
                    );
                } else {
                    return B::sendMessage(
                        [
                            "chat_id" => $this->chatid,
                            "text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>has been warned</b> (<code>".($st[0])."/".$sq[0]."</code>)",
                            "parse_mode" => "HTML"
                        ]
                    );
                }
            } else {
                $st = DB::prepare("INSERT INTO `user_warning` (`userid`,`group_id`,`uniq_id`,`reasons`,`warn_count`,`created_at`,`updated_at`) VALUES (:userid, :group_id, :uniq_id, :reasons, 1, :created_at, null);");
                $exe = $st->execute(
                    [
                        ":userid" => $this->replyto['from']['id'],
                        ":group_id" => $this->chatid,
                        ":uniq_id" => $uniq,
                        ":reasons" => json_encode([["warned_by"=>$this->userid,"reason"=>$reason,"warned_at"=>date("Y-m-d H:i:s")]]),
                        ":created_at" => date("Y-m-d H:i:s")
                    ]
                );
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die(1);
                }
                return B::sendMessage(
                    [
                            "chat_id" => $this->chatid,
                            "text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>has been warned</b> (<code>1/".$sq[0]."</code>)",
                            "parse_mode" => "HTML"
                        ]
                );
            }
        } else {
            return B::sendMessage(
                [
                    "chat_id" => $this->chatid,
                    "text" => "You are not allowed to use this command !",
                    "reply_to_message_id" => $this->msgid
                ]
            );
        }
    }

    private function __ban()
    {
        $flag = false;
        $a = json_decode(
            B::getChatAdministrators(
                [
                "chat_id" => $this->chatid
                ], "GET"
            )['content'], true
        );
        foreach ($a['result'] as $val) {
            if ($val['user']['id'] == $this->userid) {
                if ($val['status']=="creator" || $val['can_restrict_members']) {
                    $flag = true;
                }
                break;
            }
        }
        if ($flag) {
            if (isset($this->replyto['from']['username']) && strtolower($this->replyto['from']['username']) === strtolower(BOT_USERNAME)) {
                return B::sendMessage(
                    [
                        "chat_id" => $this->chatid,
                        "text" => "<b>Error</b> : \n<pre>Bad Request: user is a bot</pre>",
                        "parse_mode" => "HTML",
                        "reply_to_message_id" => $this->msgid
                    ]
                );
            } else {
                $a = B::kickChatMember(
                    [
                        "chat_id" => $this->chatid,
                        "user_id" => $this->replyto['from']['id']
                    ]
                );
                if ($a['content'] == '{"ok":true,"result":true}') {
                    return B::sendMessage(
                        [
                            "text" => '<a href="tg://user?id='.$this->userid.'">'.$this->actorcall.'</a> banned <a href="tg://user?id='.$this->replyto['from']['id'].'">'.$this->replyto['from']['first_name']."</a>!",
                            "chat_id" => $this->chatid,
                            "parse_mode" => "HTML"
                        ]
                    );
                } else {
                    return B::sendMessage(
                        [
                        "chat_id" => $this->chatid,
                        "text" => "<b>Error</b> : \n<pre>".htmlspecialchars(json_decode($a['content'], true)['description'])."</pre>",
                        "parse_mode" => "HTML",
                        "reply_to_message_id" => $this->msgid
                        ]
                    );
                }
            }
        } else {
            return B::sendMessage(
                [
                    "chat_id" => $this->chatid,
                    "text" => "You are not allowed to use this command !",
                    "reply_to_message_id" => $this->msgid
                ]
            );
        }
        return false;
    }
}

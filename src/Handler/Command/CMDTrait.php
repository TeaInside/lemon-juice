<?php

namespace Handler\Command;

use DB;
use PDO;
use Telegram as B;

trait CMDTrait
{
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
        if (B::sendVideo(
            [
                "caption" => $a,
                "video" => "https://webhooks.redangel.ga/yd/".$a,
                "chat_id" => $this->chatid,
                "reply_to_message_id" => $this->msgid
            ]
        )['info']['http_code'] != 200) {
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
        $a = explode(" ", $param, 2);
        if (trim($a[0]) == "sudo") {
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
                var_dump($st->errorInfo());
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

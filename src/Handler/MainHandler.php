<?php

namespace Handler;

use DB;
use PDO;
use Telegram as B;
use Handler\Command\Command;
use Handler\Command\CMDTrait;
use App\PHPVirtual\PHPVirtual;
use Handler\Command\MyAnimeListCMD;
use Handler\Security\PHPVirtualSecurity;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class MainHandler
{
    use PHPVirtualSecurity, Command, CMDTrait;

    /**
     * @var array
     */
    public $event;

    /**
     * @var string
     */
    public $type;
    
    /**
     * @var string
     */
    public $chattype;

    /**
     * @var array
     */
    public $from;

    /**
     * @var string
     */
    public $actor;

    /**
     * @var string
     */
    public $actorcall;

    /**
     * @var int
     */
    public $msgid;

    /**
     * @var array
     */
    public $chat;

    /**
     * @var string
     */
    public $chattitle;

    /**
     * @var string
     */
    public $chatid;

    /**
     * @var string
     */
    public $text;

    /**
     * @var string
     */
    public $lowertext;

    /**
     * @var int
     */
    public $userid;

    /**
     * @var string
     */
    public $new_actorcall;

    /**
     * Constructor.
     * @param array $event
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Parse Event.
     */
    public function parseEvent()
    {
        $this->replyto = isset($this->event['message']['reply_to_message']) ? $this->event['message']['reply_to_message'] : null;
        if (isset($this->event['message']['text'])) {
            $this->type = "text";
            $this->chattype = $this->event['message']['chat']['type'];
            $this->from = $this->event['message']['from'];
            $this->actor = $this->event['message']['from']['first_name'].(isset($this->event['message']['from']['last_name']) ? " ".$this->event['message']['from']['last_name']: "");
            $this->actorcall = $this->event['message']['from']['first_name'];
            $this->msgid = $this->event['message']['message_id'];
            $this->chat = $this->event['message']['chat'];
            $this->chattitle = isset($this->event['message']['chat']['title']) ? $this->event['message']['chat']['title'] : null;
            $this->chatid = $this->event['message']['chat']['id'];
            $this->text = $this->event['message']['text'];
            $this->lowertext = strtolower($this->text);
            $this->userid = $this->event['message']['from']['id'];
            !isset($this->from['username']) and $this->from['username'] = null;
        } elseif (isset($this->event['message']['new_chat_member'])) {
            $this->type = "new_member";
            $this->chatid = $this->event['message']['chat']['id'];
            $this->msgid = $this->event['message']['message_id'];
            $this->new_userid = $this->event['message']['new_chat_member']['id'];
            $this->new_actor  = $this->event['message']['new_chat_member']['first_name'].(isset($this->event['message']['new_chat_member']['last_name']) ? " ".$this->event['message']['new_chat_member']['last_name'] : "");
            $this->new_actorcall = $this->event['message']['new_chat_member']['first_name'];
            $this->new_from = $this->event['message']['new_chat_member'];
        }
    }

    private function filterReply()
    {
        if (isset($this->replyto)) {
            if (isset($this->replyto['from']['username']) && strtolower($this->replyto['from']['username']) == strtolower(BOT_USERNAME)) {
                $wd = explode("\n", $this->replyto['text'], 2);
                switch ($wd[0]) {
                    case 'Anime apa yang ingin kamu cari?':
                        $app = new MyAnimeListCMD($this);
                        return $app->__anime($this->lowertext);
                        break;
                    case 'Hasil pencarian anime :':
                        $app = new MyAnimeListCMD($this);
                        return $app->__idan($this->lowertext);
                        break;
                    case 'Manga apa yang ingin kamu cari?':
                        $app = new MyAnimeListCMD($this);
                        return $app->__manga($this->lowertext);
                        break;
                    case 'Hasil pencarian manga :':
                        $app = new MyAnimeListCMD($this);
                        return $app->__idma($this->lowertext);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    /**
     * Run handler.
     */
    public function runHandler()
    {
        if ($this->type == "text") {
            if ($this->filterReply()) {
            } elseif ($out = $this->checkVirtualLang()) {
                B::sendMessage(
                    [
                        "text" => $out,
                        "parse_mode" => "HTML",
                        "chat_id" => $this->chatid,
                        "reply_to_message_id" => $this->msgid
                    ]
                );
            } else {
                if (!$this->__command() and $this->chattype == "private") {
                    B::sendMessage([
                            "chat_id" => $this->chatid,
                            "text" => json_encode($this->event, 128),
                            "reply_to_message_id" => $this->msgid
                        ]);
                }
            }
        } elseif ($this->type == "new_member") {
            $st = DB::prepare("SELECT `group_id`,`group_name`,`group_username`,`welcome_message` FROM `a_known_groups` WHERE `group_id`=:gid LIMIT 1;");
            $exe = $st->execute([
                    ":gid" => $this->chatid
                ]);
            if (!$exe) {
                var_dump($st->errorInfo());
                die(1);
            }
            $st = $st->fetch(PDO::FETCH_NUM);
            if (isset($st[0]) and !empty($st[0])) {
                $a = [
                    "{group_id}",
                    "{group_name}",
                    "{group_username}",
                    "{username}",
                    "{name}",
                    "{first_name}",
                    "{userid}"
                ];
                $b = [
                    $st[0],
                    $st[1],
                    $st[2],
                    (isset($this->new_from['username']) ? $this->new_from['username'] : ""),
                    preg_replace("#[^[:print:]]#", "", $this->new_actor),
                    preg_replace("#[^[:print:]]#", "", $this->new_actorcall),
                    $this->new_userid
                ];
                B::sendMessage([
                        "chat_id" => $this->chatid,
                        "text" => str_replace($a, $b, $st[3]),
                        "reply_to_message_id" => $this->msgid,
                        "parse_mode" => "HTML",
                        "disable_web_page_preview" => true
                    ]);
            }
        }
    }

    public function __save_event()
    {
        if ($this->type == "text") {
            if ($this->chattype == "private") {
                $st = DB::prepare("INSERT INTO `private_chat` (`userid`,`time`,`message`,`created_at`) VALUES (:userid,:tm,:msg,:created_at);");
                $exe = $st->execute([
                        ":userid" => $this->userid,
                        ":tm" => date("Y-m-d H:i:s", $this->event['message']['date']),
                        ":msg" => $this->text,
                        ":created_at" => date("Y-m-d H:i:s")
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die(1);
                }
            } else {
                $st = DB::prepare("INSERT INTO `group_chat` (`group_id`,`userid`,`time`,`message`,`created_at`) VALUES (:gr, :uid, :tm, :msg, :created_at);");
                $exe = $st->execute([
                        ":gr" => $this->chatid,
                        ":uid" => $this->userid,
                        ":tm" => date("Y-m-d H:i:s", $this->event['message']['date']),
                        ":msg" => $this->text,
                        ":created_at" => date("Y-m-d H:i:s")
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die(1);
                }
                $st = DB::prepare("SELECT COUNT(`group_id`) FROM `a_known_groups` WHERE `group_id`=:gr LIMIT 1;");
                $exe = $st->execute([
                        ":gr" => $this->chatid
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die(1);
                }
                $st = $st->fetch(PDO::FETCH_NUM);
                if ($st[0] == 0) {
                    $st = DB::prepare("INSERT INTO `a_known_groups` (`group_id`,`group_name`,`group_username`,`group_link`,`welcome_message`,`msg_count`,`created_at`,`updated_at`) VALUES (:gr,:gn,:gu,:gl,:wm,:mc,:ca,:ua);");
                    $exe = $st->execute([
                            ":gr" => $this->chatid,
                            ":gn" => $this->chattitle,
                            ":gu" => (isset($this->event['message']['chat']['username']) ? $this->event['message']['chat']['username'] : null),
                            ":gl" => (isset($this->event['message']['chat']['username']) ? "https://t.me.".$this->event['message']['chat']['username'] : null),
                            ":wm" => null,
                            ":mc" => 1,
                            ":ca" => date("Y-m-d H:i:s"),
                            ":ua" => null
                        ]);
                    if (!$exe) {
                        var_dump($st->errorInfo());
                        die(1);
                    }
                } else {
                    $st = DB::prepare("UPDATE `a_known_groups` SET `group_name`=:gn, `group_username`=:gu, `group_link`=:gl,`updated_at`=:up, `msg_count`=`msg_count`+1 WHERE `group_id`=:gr LIMIT 1;");
                    $exe = $st->execute([
                        ":gr" => $this->chatid,
                        ":gn" => $this->chattitle,
                        ":gu" => (isset($this->event['message']['chat']['username']) ? $this->event['message']['chat']['username'] : null),
                        ":gl" => (isset($this->event['message']['chat']['username']) ? "https://t.me.".$this->event['message']['chat']['username'] : null),
                        ":up" => date("Y-m-d H:i:s")
                    ]);
                    if (!$exe) {
                        var_dump($st->errorInfo());
                        die(1);
                    }
                }
            }
            $st = DB::prepare("SELECT COUNT(`userid`) FROM `a_known_users` WHERE `userid`=:userid LIMIT 1;");
            $st->execute([
                    ":userid" => $this->userid
                ]);
            $st = $st->fetch(PDO::FETCH_NUM);
            if ($st[0] == 0) {
                $private = $this->chattype == "private" ? "true" : "false";
                $st = DB::prepare("INSERT INTO `a_known_users` (`userid`,`username`,`name`,`is_private_known`,`notification`,`msg_count`,`created_at`,`updated_at`) VALUES (:userid, :uname, :name, '{$private}', '{$private}', 1, :created_at, null);");
                $exe = $st->execute([
                        ":userid" => $this->userid,
                        ":uname" => strtolower($this->from['username']),
                        ":name" => $this->actor,
                        ":created_at" => (date("Y-m-d H:i:s"))
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die();
                }
            } else {
                $private = $this->chattype == "private" ? "true" : "false";
                $st = DB::prepare("UPDATE `a_known_users` SET `username`=:username, `name`=:name, `updated_at`=:ua, `msg_count`=`msg_count`+1,`is_private_known`='{$private}',`notification`='true' WHERE `userid`=:userid LIMIT 1");
                $exe = $st->execute([
                        ":username" => strtolower($this->from['username']),
                        ":userid" => $this->userid,
                        ":name" => $this->actor,
                        ":ua" => date("Y-m-d H:i:s")
                    ]);
                if (!$exe) {
                    var_dump($st->errorInfo());
                    die();
                }
            }
        }
    }

    /**
     * Check virtual lang.
     */
    private function checkVirtualLang()
    {
        if (substr($this->lowertext, 0, 5) == "<?php") {
            if ($this->__php_security()) {
                $a = str_replace(["<br />", "<br>", "<br/>"], "\n", PHPVirtual::run($this->text));
                return empty($a) ? "~" : $a;
            } else {
                return "<b>PHP Auto Rejection : </b> Rejected for security reason!";
            }
        }
    }
}

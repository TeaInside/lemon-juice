<?php

namespace Handler;

use DB;
use PDO;
use Telegram as B;
use Handler\Command\Command;
use App\PHPVirtual\PHPVirtual;
use Handler\Security\PHPVirtualSecurity;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class MainHandler
{
    use PHPVirtualSecurity, Command;

    /**
     * @var array
     */
    private $event;

    /**
     * @var string
     */
    private $type;
    
    /**
     * @var string
     */
    private $chattype;

    /**
     * @var array
     */
    private $from;

    /**
     * @var string
     */
    private $actor;

    /**
     * @var string
     */
    private $actorcall;

    /**
     * @var int
     */
    private $msgid;

    /**
     * @var array
     */
    private $chat;

    /**
     * @var string
     */
    private $chattitle;

    /**
     * @var string
     */
    private $chatid;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $lowertext;

    /**
     * @var int
     */
    private $userid;


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
        }
    }

    /**
     * Run handler.
     */
    public function runHandler()
    {
        if ($this->type == "text") {
            if ($out = $this->checkVirtualLang()) {
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
                $st = DB::prepare("UPDATE `a_known_users` SET `username`=:username, `name`=:name, `updated_at`=:ua, `msg_count`=`msg_count`+1 WHERE `userid`=:userid LIMIT 1");
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

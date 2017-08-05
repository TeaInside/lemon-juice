<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

defined("TOKEN") or require __DIR__."/../../../config/telegram.php";

use PDO;
use Sys\DB;
use Bot\Telegram\Traits\Command;
use Bot\Telegram\Traits\CommandHandler;

final class Bot
{
    use Command;
    use CommandHandler;

    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $room_id;

    /**
     * @var string
     */
    private $user_id;

    /**
     * @var uname
     */
    private $uname;

    /**
     * @var string
     */
    private $actor;

    /**
     * @var string
     */
    private $actor_call;

    /**
     * @var string
     */
    private $chat_type;

    /**
     * @var string
     */
    private $msg_id;
    
    /**
     * @var array
     */
    private $input;

    /**
     * @var string
     */
    private $msg_type;

    /**
     * @var array
     */
    private $entities = [];

    /**
     * @var array
     */
    private $entities_pos = [];

    /**
     * @var string
     */
    private $special_comannd;

    /**
     * Run bot.
     * @param string $argv
     */
    public function run($arg)
    {
        $this->input = json_decode($arg, true);
        $this->parseEvent();
        if ($this->msg_type == "text") {
            $this->parseEntities();
            $this->textFixer();
            if (!$this->command()) {
            }
            $this->notifer();
        }
        $this->knower();
    }

    /**
     * Parse webhook event.
     */
    private function parseEvent()
    {
        if (isset($this->input['message']['text'])) {
            $this->msg_type = "text";
            $this->text = $this->input['message']['text'];
            $this->room_id = $this->input['message']['chat']['id'];
            $this->user_id = $this->input['message']['from']['id'];
            $this->uname = isset($this->input['message']['from']['username']) ? $this->input['message']['from']['username'] : null;
            $this->actor = $this->input['message']['from']['first_name']. (isset($this->input['message']['from']['last_name']) ? " ".$this->input['message']['from']['last_name'] : "");
            $this->actor_call = $this->input['message']['from']['first_name'];
            $this->chat_type = $this->input['message']['chat']['type'];
            $this->msg_id = $this->input['message']['message_id'];
            $this->entities_pos = isset($this->input['message']['entities']) ? $this->input['message']['entities'] : [];
        }
    }

    /**
     * Text fixer.
     */
    private function textFixer()
    {
        $sbt = substr($this->text, 0, 4);
        if ($sbt == "ask " || $sbt == "ask\n") {
            $this->text = "/".$this->text;
        } elseif (isset($this->input['message']['reply_to_message']['from']['username']) and $this->input['message']['reply_to_message']['from']['username'] == "MyIceTea_Bot") {
            switch ($this->input['message']['reply_to_message']['text']) {
                case 'Sebutkan ID Anime yang ingin kamu cari !':
                    $this->text = "/idan ".$this->text;
                    break;
                case 'Anime apa yang ingin kamu cari? ~':
                    $this->text = "/anime ".$this->text;
                    break;
                case 'Anime apa yang ingin kamu cari?':
                    $this->text = "/qanime ".$this->text;
                    break;

                case 'Sebutkan ID Manga yang ingin kamu cari !':
                    $this->text = "/idma ".$this->text;
                    break;
                case 'Manga apa yang ingin kamu cari? ~':
                    $this->text = "/manga ".$this->text;
                    break;
                case 'Manga apa yang ingin kamu cari?':
                    $this->text = "/qmanga ".$this->text;
                    break;

                case 'Balas pesan dengan screenshot anime yang ingin kamu tanyakan !':
                    $this->special_comannd = "/whatanime";
                    break;
                default:
                        $a = explode("\n", $this->input['message']['reply_to_message']['text'], 2);
                        var_dump($a);
                        switch ($a[0]) {
                            case 'Hasil pencarian anime :':
                                $this->text = "/idan ".$this->text;
                                break;
                            case 'Hasil pencarian manga :':
                                $this->text = "/idma ".$this->text;
                                break;
                            default:
                                break;
                        }
                    break;
            }
        }
    }

    private function knower()
    {
        $is_private = $this->chat_type == "private" ? "true" : "false";
    	$pdo = DB::pdoInstance();
    	$st = $pdo->prepare("SELECT `userid`, `username`, `name`, `msg_count`, `is_private_known` FROM `a_known_users` WHERE `userid`=:userid LIMIT 1;");
    	$st->execute([
    			":userid" => $this->user_id,
    		]);
    	if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
            if ($st['is_private_known'] == "true" and $is_private == "false") {
                $is_private = "true";
            }
    		$st['msg_count']++;
    		$pdo->prepare("UPDATE `a_known_users` SET `username`=:username, `name`=:name, `msg_count`=:msg_count, `updated_at`=:up, `is_private_known`=:priv WHERE `userid`=:userid LIMIT 1;")->execute([
    				":username" => strtolower($this->uname),
    				":name" => $this->actor,
    				":msg_count" => $st['msg_count'],
    				":userid" => $this->user_id,
    				":up" => date("Y-m-d H:i:s"),
                    ":priv" => $is_private
    			]);
    	} else {
    		$pdo->prepare("INSERT INTO `a_known_users` (`userid`, `username`, `name`, `created_at`, `updated_at`, `msg_count`, `is_private_known`) VALUES (:userid, :username, :name, :created_at, :updated_at, :msg_count, :priv_known)")->execute([
    				":userid" => $this->user_id,
    				":username" => strtolower($this->uname),
    				":name" => $this->actor,
    				"created_at" => date("Y-m-d H:i:s"),
    				":updated_at"=>null,
    				":msg_count"=> 1,
                    ":priv_known" => $is_private
    			]);
    	}
    }

    private function parseEntities()
    {
        foreach ($this->entities_pos as $val) {
            if ($val['type'] == "mention") {
                $this->entities[$val['type']] = substr($this->text, $val['offset']+1, $val['length']);
            }
        }
    }

    private function notifer()
    {
        if ($this->chat_type != "private" and isset($this->entities['mention'])) {
            foreach ($this->entities['mention'] as $val) {
                if ($st = $this->check_recognized($val)) {
                    if ($st['is_private_known'] == "true") {
                        if ($st['is_notifed'] == "false") {
                            B::sendMessage("It often happens, in groups, to tag (mention) an user or to reply (quote) to one of his messages, and that he miss the related notification among all the others. In addition, if you have more than one notification all coming from the the same group, once opened the chat they're all lost forever!\n\nSo, I'll notify you when someone tags you, i.e. mentions you, using your username.", $st['userid'], null, ['parse_mode'=>"HTML"]);
                            $this->recognized($st['userid']);
                        }
                        if (isset($this->uname)) {
                            $mentioner = "@".$this->uname;
                        } else {
                            $mentioner = $this->actor_call;
                        }

                        if (isset($this->input['message']['chat']['username'])) {
                            $room = "<a href=\"".$this->input['message']['chat']['username']."\">".$this->input['message']['chat']['title']."</a>";
                            $op = ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true, "reply_markup"=>json_encode(["inline_keyboard"=>[[["text"=>"Go to the message","url"=> "https://telegram.me/".$this->input['message']['chat']['username']."/".$this->msg_id]]]])];
                        } else {
                            $room = "<b>".$this->input['message']['chat']['title']."</b>";
                            $op = ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true];
                        }
                        B::sendMessage("{$mentioner} tagged you in {$room}\n\n<pre>".htmlspecialchars($this->text)."</pre>", $st['userid'], null, $op);
                    }
                }
            }
        }
    }

    /**
     * Check recognized
     */
    private function check_recognized($username)
    {
        $st = DB::pdoInstance()->prepare("SELECT `userid`,`is_private_known`,`is_notifed` FROM `a_known_users` WHERE `username`=:username LIMIT 1;");
        $st->execute([
                ":username" => strtolower($username)
            ]);
        $st = $st->fetch(PDO::FETCH_ASSOC);        
        return $st;
    }

    private function recognized($userid)
    {
        return DB::pdoInstance()->prepare("UPDATE `a_known_users` SET `is_notifed`='true' WHERE `userid`=:userid LIMIT 1;")->execute([':userid'=>$userid]);
    }
}
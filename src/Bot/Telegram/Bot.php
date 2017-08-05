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
            $this->textFixer();
            if (!$this->command()) {
            }
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
    	$pdo = DB::pdoInstance();
    	$st = $pdo->prepare("SELECT `userid`, `username`, `name`, `msg_count` FROM `a_known_users` WHERE `userid`=:userid LIMIT 1;");
    	$st->execute([
    			":userid" => $this->user_id,
    		]);
    	if ($st = $st->fetch(PDO::FETCH_ASSOC)) {
    		$st['msg_count']++;
    		$pdo->prepare("UPDATE `a_known_users` SET `username`=:username, `name`=:name, `msg_count`=:msg_count, `updated_at`=:up WHERE `userid`=:userid LIMIT 1;")->execute([
    				":username" => strtolower($this->uname),
    				":name" => $this->actor,
    				":msg_count" => $st['msg_count'],
    				":userid" => $this->user_id,
    				":up" => date("Y-m-d H:i:s")
    			]);
    	} else {
    		$pdo->prepare("INSERT INTO `a_known_users` (`userid`, `username`, `name`, `created_at`, `updated_at`, `msg_count`) VALUES (:userid, :username, :name, :created_at, :updated_at, :msg_count)")->execute([
    				":userid" => $this->user_id,
    				":username" => strtolower($this->uname),
    				":name" => $this->actor,
    				"created_at" => date("Y-m-d H:i:s"),
    				":updated_at"=>null,
    				":msg_count"=> 1
    			]);
    	}
    }
}

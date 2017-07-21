<?php

namespace Bot\Telegram;

use IceTeaSystem\Hub\Singleton;
use Bot\Telegram\Traits\Command;
use Bot\Telegram\Traits\Callback;
use Bot\Telegram\Traits\UserWarning;
use Bot\BotContracts\TelegramContract;
use Bot\Telegram\Traits\ContractParty;
use Bot\Telegram\Traits\MessageBuilder;
use Bot\Telegram\Traits\ExtendedAction;
use Stack\Telegram\Telegram as TelegramStack;
use Bot\Telegram\Traits\WhatAnime as WhatAnimeTrait;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram
 * @since 0.0.1
 */

class Telegram implements TelegramContract
{
    /**
     * Use traits.
     */
    use Singleton, Command, MessageBuilder, WhatAnimeTrait, UserWarning, Callback, ExtendedAction, ContractParty;

    /**
     * Telegram Instance
     * @var Stack\Telegram\Telegram
     */
    private $tel;

    /**
     * Token bot. (Dapat dari @botfather)
     * @var string
     */
    private $token;

    /**
     * Input dari webhook.
     * @var string
     */
    private $webhook_input;

    /**
     * Event webhook dalam bentuk array
     * @var array
     */
    private $event;

    /**
     * @var string
     */
    private $callback_data;

    /**
     * Tipe chat (group|private)
     * @var string
     */
    private $type_chat;

    /**
     * Tipe pesan
     * @var string
     */
    private $type_msg;

    /**
     * Room ID
     * @var int
     */
    private $room;

    /**
     * @var array
     */
    private $entities = array();

    /**
     * @var array
     */
    private $reply = array();

    /**
     * @var string
     */
    private $actor_call;

    /**
     * @var array
     */
    private $extended_commands = array();

    /**
     * @var array
     */
    private $pending_action = array();

    /**
     * @var array
     */
    private $whatanime_salt_hash_table = array();

    /**
     * @var int
     */
    private $actor_id;

    /**
     * Constructor
     */
    public function __construct($token)
    {
        set_time_limit(0);
        ignore_user_abort(true);
        ini_set("max_execution_time", false);
        ini_set("memory_limit", "4G");
        $this->token = $token;
        $this->tel = new TelegramStack($token);
        is_dir(storage."/telegram") or mkdir(storage."/telegram");
    }

    /**
     * Jalankan bot.
     */
    public static function run($token)
    {
        $self = self::getInstance($token);
        $self->getEvent();
        $self->execute();
        $self->logs();
    }

    /**
     * Eksekusi event
     */
    private function execute()
    {
        $this->parseEvent();
        if ($this->type_msg == "callback_query") {
            $this->parseCallback();
        } else {
            $this->parseEntities();
            $this->parseReply();
            $this->parseCommand();
            if (count($this->reply)==0 and isset($this->event['message']['text'])) {
                $this->parseWords();
                $this->parseExtendedAction();
                if (count($this->reply)==0 and $this->type_chat=="private") {
                    $this->textReply("Mohon maaf, saya belum mengerti \"{$this->event['message']['text']}\"");
                }
            }
        }
        /*// debugging only :v
        var_dump($this->reply);
        die;
        */

        $this->replyAction();
    }

    /**
     * Ambil event dari webhook.
     */
    private function getEvent()
    {
        $this->webhook_input = '{
    "update_id": 344180636,
    "message": {
        "message_id": 513,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001128531173,
            "title": "LTM Group",
            "type": "supergroup"
        },
        "date": 1500652940,
        "text": "halo",
        "entities": [
            {
                "type": "abot_command",
                "offset": 0,
                "length": 6
            }
        ]
    }
}';
       /* if (defined("webhook_input")) {
            $this->webhook_input = file_get_contents(webhook_input);
        } else {
            $this->webhook_input = file_get_contents("php://input");
        }*/
        $this->event = json_decode($this->webhook_input, true);
    }

    /**
     * Parse event
     */
    private function parseEvent()
    {
        if (isset($this->event['message']['text'])) {
            $this->type_chat = $this->event['message']['chat']['type'];
            $this->type_msg = "text";
            $this->actor = $this->event['message']['from']['first_name'].(isset($this->event['message']['from']['last_name']) ? " ".$this->event['message']['from']['last_name']:"");
            $this->room = $this->event['message']['chat']['id'];
            $this->actor_call = $this->event['message']['from']['first_name'];
            $this->actor_id = $this->event['message']['from']['id'];
        } elseif (isset($this->event['callback_query'])) {
            $this->type_msg = "callback_query";
            $this->callback_data = $this->event['callback_query']['data'];
            $this->room = $this->event['callback_query']['message']['chat']['id'];
        }
    }

    /**
     * Parse kata
     */
    private function parseWords()
    {
        isset($this->event['message']['text']) and $this->exploded = explode(" ", $this->event['message']['text']);
    }


    /**
     * Proses semua balasan.
     */
    private function replyAction()
    {
        foreach ($this->reply as $key => $val) {
            if ($val['type'] == "text") {
                var_dump($val);
                $val['to'] = $val['to']===null ? $this->event['message']['chat']['id'] : $val['to'];
                if (is_array($val['content'])) {
                    foreach ($val['content'] as $msg) {
                        $this->tel->sendMessage($msg, $val['to'], $val['reply_to'], $val['option']);
                    }
                } else {
                    $aa = $this->tel->sendMessage($val['content'], $val['to'], $val['reply_to'], $val['option']);
                }
                var_dump($aa);
            } elseif ($val['type'] == "image") {
                $val['to'] = $val['to']===null ? $this->event['message']['chat']['id'] : $val['to'];
                if (is_array($val['content'])) {
                    foreach ($val['content'] as $photo) {
                        $this->tel->sendPhoto($photo, $val['to'], null, $val['reply_to'], $val['option']);
                    }
                } else {
                    $this->tel->sendPhoto($val['content'], $val['to'], null, $val['reply_to'], $val['option']);
                }
            }
        }
    }

    /**
     * Parse User Reply
     */
    private function parseReply()
    {
        if (isset($this->event['message']['reply_to_message'])) {
            $rtm = $this->event['message']['reply_to_message'];
            if ($rtm['from']['username'] == "MyIceTea_Bot") {
                $text = $rtm['text'];
                $a = explode("\n", $text, 2);
                if ($a[0] == "Hasil pencarian anime :" || $a[0] == "Sebutkan ID Anime yang ingin kamu cari !") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/idan",
                            "salt"    => $this->event['message']['text'],
                        );
                } elseif ($a[0] == "Anime apa yang ingin kamu cari? ~") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/anime",
                            "salt"    => $this->event['message']['text'],
                        );
                } elseif ($a[0] == "Anime apa yang ingin kamu cari?") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/qanime",
                            "salt"    => $this->event['message']['text'],
                        );
                } elseif ($a[0] == "Hasil pencarian manga :" || $a[0] == "Sebutkan ID Manga yang ingin kamu cari !") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/idma",
                            "salt"    => $this->event['message']['text'],
                        );
                } elseif ($a[0] == "Manga apa yang ingin kamu cari? ~") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/manga",
                            "salt"    => $this->event['message']['text'],
                        );
                } elseif ($a[0] == "Balas pesan dengan screenshot anime yang ingin kamu tanyakan !") {
                    $this->entities['bot_command'][] = array(
                            "command" => "/whatanime",
                            "salt"    => (isset($this->event['message']['photo'][1]) ? $this->getPhotoUrl($this->event['message']['photo'][1]['file_id']) : ((isset($this->event['message']['text']) and filter_var(str_replace(" ", urlencode(" "), $this->event['message']['text']), FILTER_VALIDATE_URL)) ? $this->event['message']['text'] : false)),
                        );
                }
            }
        }
    }

    /**
     * Ambil url photo dari ID file
     *
     * @return string | false
     */
    private function getPhotoUrl($photo_id)
    {
        $a = json_decode($this->tel->getFile($photo_id), true);
        return isset($a['result']['file_path']) ? "https://api.telegram.org/file/bot".TELEGRAM_TOKEN."/".$a['result']['file_path'] : false;
    }

    /**
     * **Future
     * Parse ExtendedCommand
     * @param array $val
     */
    private function parseExtendedCommand($val)
    {
    }

    /**
     * Parse mention, bot_command dan hashtag.
     *
     * Void.
     */
    private function parseEntities()
    {
        if (isset($this->event['message']['entities'])) {
            $text = $this->event['message']['text'];
            $entities = $this->event['message']['entities'];
            $count = count($entities);
            for ($i=0; $i < $count; $i++) {
                if ($entities[$i]['type'] == "bot_command") {
                    $ofplg = $entities[$i]['offset']+$entities[$i]['length'];
                    $endsalt = (isset($entities[$i+1]) && $entities[$i+1]['type']!="url" ? $entities[$i+1]['offset']-$ofplg-2: strlen($text));
                    $cmd = explode("@", substr($text, $entities[$i]['offset'], $ofplg));
                    $salt = substr($text, $ofplg+1, $endsalt);
                    $this->entities['bot_command'][] = array(
                            "command" => $cmd[0],
                            "salt"      => ($salt===false ? "" : $salt),
                        );
                } elseif ($entities[$i]['type'] == "mention") {
                    $this->entities['mention'][] = substr($text, $entities[$i]['offset']+1, $entities[$i]['length']);
                } elseif ($entities[$i]['type'] == "hashtag") {
                    $this->entities['hashtag'][] = substr($text, $entities[$i]['offset']+1, $entities[$i]['length']);
                }
            }
        }
    }

    /**
     * Simpan log
     *
     * void
     */
    private function logs()
    {
        file_put_contents(logs."/telegram_body.txt", json_encode(json_decode($this->webhook_input), 128)."\n\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * Disini kita override method getInstance dari trait Singleton.
     */
    private static function getInstance($token)
    {
        if (self::$instance === null) {
            self::$instance = new self($token);
        }
        return self::$instance;
    }
}

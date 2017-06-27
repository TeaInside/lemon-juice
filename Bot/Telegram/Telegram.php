<?php

namespace Bot\Telegram;

use IceTeaSystem\Hub\Singleton;
use App\MyAnimeList\MyAnimeList;
use Bot\BotContracts\TelegramContract;
use Stack\Telegram\Telegram as TelegramStack;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram
 * @since 0.0.1
 */

class Telegram implements TelegramContract
{
    /**
     * Pakai singleton pattern.
     */
    use Singleton;

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
     * Constructor
     */
    public function __construct($token)
    {
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
        $this->parseWords();
        $this->parseEntities();
        $this->parseReply();
        $this->parseCommand();
        if (count($this->reply)==0 and $this->type_chat=="private") {
            $this->textReply("Mohon maaf, saya belum mengerti \"{$this->event['message']['text']}\"");
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
        /*$this->webhook_input = '
        {
        "update_id": 344173952,
        "message": {
        "message_id": 315,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "type": "private"
        },
        "date": 1498583326,
        "reply_to_message": {
            "message_id": 314,
            "from": {
                "id": 448907482,
                "first_name": "IceTeaNime",
                "username": "MyIceTea_Bot"
            },
            "chat": {
                "id": 243692601,
                "first_name": "Ammar",
                "last_name": "Faizi",
                "username": "ammarfaizi2",
                "type": "private"
            },
            "date": 1498583305,
            "text": "Anime apa yang ingin kamu cari?"
        },
        "text": "full metal al"
        }
        }';*/
        $this->webhook_input = file_get_contents("php://input");
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
        }
    }

    /**
     * Parse kata
     */
    private function parseWords()
    {
        $this->exploded = explode(" ", $this->event['message']['text']);
    }

    /**
     * Builder : Balasan text
     */
    private function textReply($text, $to=null, $reply_to=null, $option=null)
    {
        $this->reply[] = array(
                "type"=>"text",
                "reply_to"=>$reply_to,
                "to"=>($to===null?$this->room:$to),
                "content"=>$text,
                "option"=>$option
            );
    }

    /**
     * Builder : Balasan gambar
     */
    private function imageReply($text, $to=null, $reply_to=null, $option=null)
    {
        $this->reply[] = array(
                "type"=>"image",
                "reply_to"=>$reply_to,
                "to"=>($to===null?$this->room:$to),
                "content"=>$text,
                "option"=>$option
            );
    }

    /**
     * Jalankan balasan.
     */
    private function replyAction()
    {
        foreach ($this->reply as $key => $val) {
            if ($val['type'] == "text") {
                if (is_array($val['content'])) {
                    foreach ($val['content'] as $msg) {
                        $this->tel->sendMessage($msg, $val['to'], $val['reply_to'], $val['option']);
                    }
                } else {
                    $this->tel->sendMessage($val['content'], $val['to'], $val['reply_to'], $val['option']);
                }
            } elseif ($val['type'] == "image") {
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
     * Parse Command
     */
    private function parseCommand()
    {
        $list = array(
                "/start",
                "/help",
                "/qanime",
                "/anime",
                "/idan",
                "/qmanga",
                "/manga",
                "/idma",
                "/test"
            );
        if (file_exists(storage."/telegram/extended_keywords.json")) {
            $a = json_decode(file_get_contents(storage."/telegram/extended_keywords.json"), true);
            $a = isset($a['keywords']) ? $a['keywords'] : array();
            $list = array_merge($a, $list);
            $this->extended_commands = $a['entry'];
        }
        if (isset($this->entities['bot_command'])) {
            foreach ($this->entities['bot_command']    as $val) {
                switch ($val['command']) {
                case '/start':
                    $this->textReply("Hai ".$this->actor_call.", ketik /help untuk menampilkan menu.");
                    break;
                case '/help':
                    $this->textReply("Hai ".$this->actor_call.", menu :\n/anime [spasi] [judul] : Pencarian anime secara rinci.\n/idan [spasi] [id_anime] : Pencarian info anime secara lengkap menggunakan id_anime.\n/qanime [spasi] [judul] : Pencarian anime secara instant.");
                    break;
                case '/qanime':
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $fx = function ($str) {
                            if (is_array($str)) {
                                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                            }
                            return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
                        };
                            $st = (new MyAnimeList(MAL_USER, MAL_PASS))->simple_search($val['salt']);
                        if (is_array($st) and count($st)) {
                            $img = $st['image'];
                            unset($st['image']);
                            $rep = "";
                            foreach ($st as $key => $value) {
                                $ve = $fx($value);
                                !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                            }
                            $this->imageReply($img, null, $this->event['message']['message_id']);
                            $this->textReply(str_replace("\n\n", "\n", $rep), null, null, array("parse_mode"=>"HTML"));
                        } else {
                            $this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Anime apa yang ingin kamu cari?", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                case '/anime':
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $st = new MyAnimeList(MAL_USER, MAL_PASS);
                        $st->search($val['salt']);
                        $st->exec();
                        $st = $st->get_result();
                        if (isset($st['entry']['id'])) {
                            $rep = "";
                            $rep.="Hasil pencarian :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idan [spasi] [id_anime] untuk menampilkan info anime lebih lengkap.";
                            $this->textReply(
                                $rep, null, $this->event['message']['message_id'], array(
                                "parse_mode"=>"HTML",
                                "reply_markup"=>json_encode(
                                    array(
                                            "force_reply"=>true,
                                            "selective"=>true
                                        )
                                )
                                    )
                            );
                        } elseif (is_array($st) and $xz = count($st['entry'])) {
                            $rep = "Hasil pencarian :\n";
                            foreach ($st['entry'] as $vz) {
                                $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                            }
                            $rep.="\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idan [spasi] [id_anime] untuk menampilkan info anime lebih lengkap.";
                            $this->textReply(
                                $rep, null, $this->event['message']['message_id'], array(
                                "parse_mode"=>"HTML",
                                "reply_markup"=>json_encode(
                                    array(
                                            "force_reply"=>true,
                                            "selective"=>true
                                        )
                                )
                                    )
                            );
                        } else {
                            $this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Anime apa yang ingin kamu cari? ~", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                case "/idan":
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $fx = function ($str) {
                            if (is_array($str)) {
                                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                            }
                            return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
                        };
                            $st = new MyAnimeList(MAL_USER, MAL_PASS);
                            $st = $st->get_info($val['salt']);
                            $st = isset($st['entry']) ? $st['entry'] : $st;
                        if (is_array($st) and count($st)) {
                            $img = $st['image'];
                            unset($st['image']);
                            $rep = "";
                            foreach ($st as $key => $value) {
                                $ve = $fx($value);
                                !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                            }
                            $this->imageReply($img, null, $this->event['message']['message_id']);
                            $this->textReply(str_replace("\n\n", "\n", $rep), null, null, array("parse_mode"=>"HTML"));
                        } else {
                            $this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Sebutkan ID Anime yang ingin kamu cari !", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                case '/qmanga':
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $fx = function ($str) {
                            if (is_array($str)) {
                                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                            }
                            return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
                        };
                            $st = (new MyAnimeList(MAL_USER, MAL_PASS))->simple_search($val['salt'], "manga");
                        if (is_array($st) and count($st)) {
                            $img = $st['image'];
                            unset($st['image']);
                            $rep = "";
                            foreach ($st as $key => $value) {
                                $ve = $fx($value);
                                !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                            }
                            $this->imageReply($img, null, $this->event['message']['message_id']);
                            $this->textReply(str_replace("\n\n", "\n", $rep), null, null, array("parse_mode"=>"HTML"));
                        } else {
                            $this->textReply("Mohon maaf, manga \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Manga apa yang ingin kamu cari?", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                case '/manga':
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $st = new MyAnimeList(MAL_USER, MAL_PASS);
                        $st->search($val['salt'], "manga");
                        $st->exec();
                        $st = $st->get_result();
                        if (isset($st['entry']['id'])) {
                            $rep = "";
                            $rep.="Hasil pencarian :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah manga yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idma [spasi] [id_anime] untuk menampilkan info manga lebih lengkap.";
                            $this->textReply(
                                $rep, null, $this->event['message']['message_id'], array(
                                "parse_mode"=>"HTML",
                                "reply_markup"=>json_encode(
                                    array(
                                            "force_reply"=>true,
                                            "selective"=>true
                                        )
                                )
                                    )
                            );
                        } elseif (is_array($st) and $xz = count($st['entry'])) {
                            $rep = "Hasil pencarian :\n";
                            foreach ($st['entry'] as $vz) {
                                $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                            }
                            $rep.="\nBerikut ini adalah beberapa manga yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idma [spasi] [id_manga] untuk menampilkan info manga lebih lengkap.";
                            $this->textReply(
                                $rep, null, $this->event['message']['message_id'], array(
                                "parse_mode"=>"HTML",
                                "reply_markup"=>json_encode(
                                    array(
                                            "force_reply"=>true,
                                            "selective"=>true
                                        )
                                )
                                    )
                            );
                        } else {
                            $this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Anime apa yang ingin kamu cari? ~", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                case "/idma":
                        $val['salt'] = trim($val['salt']);
                    if (!empty($val['salt'])) {
                        $fx = function ($str) {
                            if (is_array($str)) {
                                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                            }
                            return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
                        };
                            $st = new MyAnimeList(MAL_USER, MAL_PASS);
                            $st = $st->get_info($val['salt'], "manga");
                            $st = isset($st['entry']) ? $st['entry'] : $st;
                        if (is_array($st) and count($st)) {
                            $img = $st['image'];
                            unset($st['image']);
                            $rep = "";
                            foreach ($st as $key => $value) {
                                $ve = $fx($value);
                                !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                            }
                            $this->imageReply($img, null, $this->event['message']['message_id']);
                            $this->textReply(str_replace("\n\n", "\n", $rep), null, null, array("parse_mode"=>"HTML"));
                        } else {
                            $this->textReply("Mohon maaf, manga \"{$val['salt']}\" tidak ditemukan !");
                        }
                    } else {
                        $this->textReply(
                            "Sebutkan ID Manga yang ingin kamu cari !", null, $this->event['message']['message_id'], array(
                                "reply_markup"=>json_encode(
                                    array(
                                        "force_reply"=>true,
                                        "selective"=>true
                                        )
                                )
                                )
                        );
                    }
                    break;
                default:
                        count($this->extended_commands) and $this->parseExtendedCommand($val);
                    break;
                }
            }
        }
    }

    /**
     *
     */
    private function parseReply()
    {
        if (isset($this->event['message']['reply_to_message'])) {
            $rtm = $this->event['message']['reply_to_message'];
            if ($rtm['from']['username'] == "MyIceTea_Bot") {
                $text = $rtm['text'];
                $a = explode("\n", $text, 2);
                if ($a[0] == "Hasil pencarian :" || $a[0] == "Sebutkan ID Anime yang ingin kamu cari !") {
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
                }
            }
        }
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
                    $endsalt = (isset($entities[$i+1]) ? $entities[$i+1]['offset']-$ofplg-2: strlen($text));
                    $cmd = explode("@", substr($text, $entities[$i]['offset'], $ofplg));
                    $this->entities['bot_command'][] = array(
                            "command" => $cmd[0],
                            "salt"      => substr($text, $ofplg+1, $endsalt),
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

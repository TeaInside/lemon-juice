<?php

namespace Bot\Telegram;

use IceTeaSystem\Curl;
use App\WhatAnime\WhatAnime;
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
     * @var array
     */
    private $pending_action = array();

    /**
     * @var array
     */
    private $whatanime_salt_hash_table = array();

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
        $this->execPendingAction();
    }

    /**
     * Ambil event dari webhook.
     */
    private function getEvent()
    {
        $this->webhook_input = '{
    "update_id": 344174155,
    "message": {
        "message_id": 672,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -209639625,
            "title": "Test Driven Development",
            "type": "group",
            "all_members_are_administrators": true
        },
        "date": 1498637239,
        "text": "\/whatanime",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 10
            }
        ]
    }
}
';
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
        isset($this->event['message']['text']) and $this->exploded = explode(" ", $this->event['message']['text']);
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
                $val['to'] = $val['to']===null ? $this->event['message']['chat']['id'] : $val['to'];
                if (is_array($val['content'])) {
                    foreach ($val['content'] as $msg) {
                        $this->tel->sendMessage($msg, $val['to'], $val['reply_to'], $val['option']);
                    }
                } else {
                   $aa = $this->tel->sendMessage($val['content'], $val['to'], $val['reply_to'], $val['option']);
                }
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

    private function execPendingAction()
    {
        foreach ($this->pending_action as $val) {
            $val();
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
                "/whatanime"
            );
        if (file_exists(storage."/telegram/extended_keywords.json")) {
            $a = json_decode(file_get_contents(storage."/telegram/extended_keywords.json"), true);
            $a = isset($a['keywords']) ? $a['keywords'] : array();
            $list = array_merge($a, $list);
            $this->extended_commands = $a['entry'];
        }
        if (isset($this->entities['bot_command'])) {
            $this->room = $this->room===null ? $this->event['message']['chat']['id']:$this->root;
            foreach ($this->entities['bot_command']    as $enkey => $val) {
                switch ($val['command']) {
                case '/start':
                    $this->textReply("Hai ".$this->actor_call.", ketik /help untuk menampilkan menu.");
                    break;
                case '/help':
                    $this->textReply("Hai ".$this->actor_call.", menu yang tersedia :\n\n<b>Anime</b>\n/anime [spasi] [judul] : Pencarian anime secara rinci.\n/idan [spasi] [id_anime] : Pencarian info anime secara lengkap menggunakan id_anime.\n/qanime [spasi] [judul] : Pencarian anime secara instant.\n\n<b>Manga</b>\n/manga [spasi] [judul] : Pencarian manga secara rinci.\n/idma [spasi] [id_manga] : Pencarian info manga secara lengkap menggunakan id_manga.\n/qmanga [spasi] [judul] : Pencarian manga secara instant.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
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
                            $rep.="Hasil pencarian anime :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idan [spasi] [id_anime] untuk menampilkan info anime lebih lengkap.";
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
                            $rep = "Hasil pencarian anime :\n";
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
                            $rep.="Hasil pencarian manga :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah manga yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idma [spasi] [id_anime] untuk menampilkan info manga lebih lengkap.";
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
                            $rep = "Hasil pencarian manga :\n";
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
                            "Manga apa yang ingin kamu cari? ~", null, $this->event['message']['message_id'], array(
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
                case '/whatanime':
                    $val['salt'] = $val['salt']===false ? false : trim($val['salt']);
                    if (!empty($val['salt']) && $val['salt']!==false) {
                            is_dir("video") or mkdir("video");
                            $this->load_whatanime_data();
                            $file = (new Curl($val['salt']))->exec();
                            $file_hash = sha1($file);
                            if (!isset($this->whatanime_hash_table['file_hash'][$file_hash])) {
                                $st = new WhatAnime($file, "real");
                                $st = json_decode($st->exec(), true);
                                $this->whatanime_hash_table['file_hash'][$file_hash] = array(
                                        "docs" => (isset($st['docs'][0]) ? $st['docs'][0] : false)
                                    );
                                $this->save_whatanime_hash();
                            } else {
                                $st['docs'][0] = $this->whatanime_hash_table['file_hash'][$file_hash]['docs'];
                            }
                            if (isset($st['docs'][0]) && $st['docs'][0] !==false) {
                                $a = $st['docs'][0];
                                $rep = "Anime yang mirip :\n\n<b>Judul</b> : ".$a['title']."\n";
                                isset($a['title_english']) and $rep.="<b>Judul Inggris</b> : ".$a['title_english']."\n";
                                isset($a['title_romaji']) and $rep.="<b>Judul Romanji</b> : ".$a['title_romaji']."\n";
                                isset($a['episode']) and $rep.= "<b>Episode</b> : ".$a['episode']."\n";
                                isset($a['season']) and $rep.= "<b>Season</b> : ".$a['season']."\n";
                                isset($a['anime']) and $rep.= "<b>Anime</b> : ".$a['anime']."\n";
                                isset($a['file']) and $rep.= "<b>File</b> : ".$a['file'];
                                $video_url = "https://whatanime.ga/".$a['season']."/".$a['anime']."/".$a['file']."?start=".$a['start']."&end=".$a['end']."&token=".$a['token'];
                                #var_dump($st['docs'][0]);die;
                                $this->textReply($rep, null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                                $this->replyAction();
                                $this->reply = array();
                                $file = $a['file'];
                                $dur = array(
                                    "start"=>$a['start'],
                                    "end"=>$a['end']
                                );
                                ignore_user_abort(1);
                                set_time_limit(0);
                                ini_set("max_execution_time", false);
                                $hash_fn = sha1($a['season']."/".$a['anime']."/".$a['file']."?start=".$a['start']."&end=".$a['end']);
                                if (!file_exists("video/".$hash_fn.".mp4")) {
                                    $a = new Curl($video_url);
                                    $a->set_opt(
                                        array(
                                            CURLOPT_REFERER => "https://whatanime.ga/",
                                            CURLOPT_HTTPHEADER => array(
                                                "X-Requested-With: XMLHttpRequest",
                                                "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
                                            )
                                            )
                                    );
                                    $video = $a->exec();
                                    if (isset($this->whatanime_hash_table['video_hash']) and count($this->whatanime_hash_table['video_hash']) >= 10) {
                                        unlink($this->whatanime_hash_table['video_hash'][0]);
                                        file_put_contents("video/".$hash_fn.".mp4", $video);
                                        $this->whatanime_hash_table['video_hash'][0] = $hash_fn;
                                    } else {
                                        file_put_contents("video/".$hash_fn.".mp4", $video);
                                        $this->whatanime_hash_table['video_hash'][] = $hash_fn;
                                    }
                                    $this->save_whatanime_hash();
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
                                    file_put_contents("debug_dur.txt", json_encode($dur));
                                    $x = $this->tel->sendVideo("https://www.crayner.cf/.webhooks/IceTea/public/Telegram/video/".$hash_fn.".mp4", $this->room, "Berikut ini adalah cuplikan singkat dari anime yang mirip.\n\nDurasi : ".$fd($dur['start'])." - ".$fd($dur['end']), $this->event['message']['message_id']);
                                    file_put_contents("debug_video.txt", $x);
                            } else {
                                $this->textReply("Mohon maaf, pencarian tidak ditemukan !", null, $this->event['message']['message_id']);
                            }
                    } else {
                        if (isset($this->event['message']['reply_to_message']['photo'][1])) {
                            $this->entities['bot_command'][$enkey] = array(
                                    "command" => "/whatanime",
                                    "salt" => $this->getPhotoUrl($this->event['message']['reply_to_message']['photo'][1]['file_id'])
                                );
                            $this->event['message']['message_id'] = $this->event['message']['reply_to_message']['message_id'];
                            $this->parseCommand();
                        } else {
                            if ($val['salt'] === false) {
                                $this->textReply("Mohon maaf, pencarian tidak ditemukan !", null, $this->event['message']['message_id']);
                            } else {
                                $this->textReply(
                                "Balas pesan dengan screenshot anime yang ingin kamu tanyakan !", null, $this->event['message']['message_id'], array(
                                    "reply_markup"=>json_encode(
                                        array(
                                            "force_reply"=>true,
                                            "selective"=>true
                                            )
                                        )
                                    )
                                );
                            }
                        }
                    }
                    break;
                default:
                        count($this->extended_commands) and $this->parseExtendedCommand($val);
                    break;
                }
            }
        }
    }

    private function save_whatanime_hash()
    {
        file_put_contents("whatanime_hash_table.json", json_encode($this->whatanime_hash_table, 128));
    }

    private function load_whatanime_data()
    {
        if (file_exists("whatanime_hash_table.json")) {
            $this->whatanime_hash_table = json_decode(file_get_contents("whatanime_hash_table.json"), true);
            $this->whatanime_hash_table = is_array($this->whatanime_hash_table) ? $this->whatanime_hash_table : array();
        } else {
            $this->whatanime_hash_table = array();
        }
    }

    private function pendingAction($closure)
    {
        $this->pending_action[] = $closure;
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
                            "salt"    => (isset($this->event['message']['photo'][1]) ? $this->getPhotoUrl($this->event['message']['photo'][1]['file_id']) : false),
                        );
                }
            }
        }
    }

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

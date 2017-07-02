<?php

namespace Bot\Telegram\Traits;

use IceTeaSystem\Curl;
use App\MyAnimeList\MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait Command
{
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
                "/whatanime",
                "/kick",
                "/warn"
            );
        if (file_exists(storage."/telegram/extended_keywords.json")) {
            $a = json_decode(file_get_contents(storage."/telegram/extended_keywords.json"), true);
            $a = isset($a['keywords']) ? $a['keywords'] : array();
            $list = array_merge($a, $list);
            $this->extended_commands = $a['entry'];
        }
        if (isset($this->entities['bot_command'])) {
            $this->room = $this->room===null ? $this->event['message']['chat']['id']:$this->room;
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
                                if (isset($this->whatanime_hash_table['video_hash']) and count($this->whatanime_hash_table['video_hash']) >= 30) {
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
                case '/kick':
                    if (isset($this->event['message']['reply_to_message']['from']['id'])) {
                        $check_admin = strpos($this->tel->getChatAdministrators($this->room), (string)$this->event['message']['from']['id'])!==false;
                        if ($check_admin) {
                            $this->tel->kickChatMember($this->room, $this->event['message']['reply_to_message']['from']['id']);
                            $this->textReply("Siap kang <b>".$this->actor_call."</b> !\n@".$this->event['message']['reply_to_message']['from']['username']." telah ditendang !", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                        } else {
                            $this->textReply("Kamu itu bukan admin @".$this->event['message']['from']['username']." :p", $this->event['message']['chat']['id'], $this->event['message']['message_id']);
                        }
                    }
                    break;
                case '/warn':
                    if (isset($this->event['message']['reply_to_message']['from']['id'])) {
                        $check_admin = strpos($this->tel->getChatAdministrators($this->room), (string)$this->event['message']['from']['id'])!==false;
                        if ($check_admin) {
                            $uifo = $this->event['message']['reply_to_message']['from']['id']."_".$this->room;
                            $warning_count = $this->count_user_warning($uifo)+1;
                            if ($warning_count>=5) {
                                $this->tel->kickChatMember($this->room, $this->event['message']['reply_to_message']['from']['id']);
                                $this->textReply("Siap kang <b>".$this->actor_call."</b> !\n@".$this->event['message']['reply_to_message']['from']['username']." telah ditendang karena telah melewati batas warning !", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                                $this->user_warning_data[$uifo] = 0;
                            } else {
                                $this->load_callback_flag_data();
                                $callback_flag = time();
                                $this->textReply(
                                    "@".$this->event['message']['reply_to_message']['from']['username']." anda diperingatkan !\n\n<b>Harap jangan diulangi lagi !</b>\n\nJumlah peringatan <b>".($warning_count)."</b> dari <b>5</b>", null, $this->event['message']['reply_to_message']['message_id'],
                                    array(
                                    "parse_mode"=>"HTML",
                                    "reply_markup"=>json_encode(
                                        array("inline_keyboard"=>array(
                                                array(
                                                    array(
                                                        "text"=>"Batalkan peringatan",
                                                        "callback_data"=>json_encode(
                                                            array(
                                                                "cmd"=>"cw",
                                                                "c"=>$uifo,
                                                                "f"=>$callback_flag
                                                                )
                                                        )
                                                        ),
                                                    array(
                                                        "text"=>"Reset peringatan",
                                                        "callback_data"=>json_encode(
                                                            array(
                                                                "cmd"=>"rw",
                                                                "c"=>$uifo,
                                                                "f"=>$callback_flag
                                                                )
                                                        )
                                                        )
                                                    )
                                                )
                                            )
                                    )
                                    )
                                );
                                $this->user_warning_data[$uifo] = $warning_count;
                                $this->callback_flag_data[$callback_flag] = false;
                                $this->save_callback_flag();
                            }
                            $this->save_warning_data();
                        } else {
                            $this->textReply("Kamu itu bukan admin, @".$this->event['message']['from']['username']." :p", $this->event['message']['chat']['id'], $this->event['message']['message_id']);
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
}

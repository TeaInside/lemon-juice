<?php

namespace Handler\Command;

use Telegram as B;
use Handler\MainHandler;
use App\MyAnimeList\MyAnimeList;

class MyAnimeListCMD
{
    /**
     * @see Handler\MainHandler
     * @var Handler\MainHandler
     */
    private $hd;

    /**
     * Constructor.
     *
     * @param MainHandler $handler_instance
     */
    public function __construct(MainHandler $handler_instance)
    {
        $this->hd = $handler_instance;
    }

    /**
     * @param string
     */
    public function __anime($query)
    {
        if (!empty($query)) {
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st->search($query);
            $st->exec();
            $st = $st->get_result();
            if (isset($st['entry']['id'])) {
                $rep = "";
                $rep.="Hasil pencarian anime :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$id}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.";
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian anime :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                $rep.="\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$query}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.";
            } else {
                $rep = "Mohon maaf, anime \"{$query}\" tidak ditemukan !";
                $noforce = true;
            }
            return B::sendMessage([
                "chat_id" => $this->hd->chatid,
                "text" => $rep,
                "parse_mode" => "HTML",
                "disable_web_page_preview" => true,
                "reply_markup" => (isset($noforce) ? null : json_encode(["force_reply"=>true,"selective"=>true]))
            ]);
        } else {
            return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => "Anime apa yang ingin kamu cari?",
                    "reply_markup"=>(json_encode(["force_reply"=>true,"selective"=>true])),
                    "reply_to_message_id" => $this->hd->msgid
                ]);
        }
    }

    public function __idan($id)
    {
        if (!empty($id)) {
            $fx = function ($str) {
                if (is_array($str)) {
                    return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                }
                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
            };
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st = $st->get_info($id);
            $st = isset($st['entry']) ? $st['entry'] : $st;
            if (is_array($st) and count($st)) {
                $img = $st['image'];
                unset($st['image']);
                $rep = "";
                foreach ($st as $key => $value) {
                    $ve = $fx($value);
                    !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                }
                $rep = str_replace("\n\n", "\n", $rep);
            } else {
                $rep = "Mohon maaf, anime dengan id \"{$id}\" tidak ditemukan !";
            }
            isset($img) and B::sendPhoto([
                    "chat_id" => $this->hd->chatid,
                    "photo" => $img,
                    "reply_to_message_id" => $this->hd->msgid
                ]);
            return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => $rep,
                    "reply_to_message_id" => $this->hd->msgid,
                    "parse_mode" => "HTML"
                ]);
        } else {
            return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => "Sebutkan ID Anime yang ingin kamu cari !",
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true]),
                    "reply_to_message_id" => $this->hd->msgid
                ]);
        }
    }

    public function __manga($query)
    {
        if (!empty($query)) {
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st->search($query, "manga");
            $st->exec();
            $st = $st->get_result();
            if (isset($st['entry']['id'])) {
                $rep = "";
                $rep.="Hasil pencarian manga :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah manga yang cocok dengan <b>{$query}</b>.\n\nKetik /idma [spasi] [id_anime] atau balas dengan id manga untuk menampilkan info manga lebih lengkap.";
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian manga :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                $rep.="\nBerikut ini adalah beberapa manga yang cocok dengan <b>{$query}</b>.\n\nKetik /idma [spasi] [id_manga] atau balas dengan id manga untuk menampilkan info manga lebih lengkap.";
            } else {
                $rep = "Mohon maaf, anime \"{$query}\" tidak ditemukan !";
            }
            return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => $rep,
                    "parse_mode" => "HTML",
                    "disable_web_page_preview" => true,
                    "reply_markup" => (isset($noforce) ? null : json_encode(["force_reply"=>true,"selective"=>true]))
                ]);
        } else {
            return B::sendMessage([
                    "text" => "Manga apa yang ingin kamu cari?",
                    "chat_id" => $this->hd->chatid,
                    "reply_markup" => json_encode(["force_reply"=>true,"selective"=>true])
                ]);
        }
    }

    public function __idma($id)
    {
        if (!empty($id)) {
            $fx = function ($str) {
                if (is_array($str)) {
                    return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                }
                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
            };
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st = $st->get_info($id, "manga");
            $st = isset($st['entry']) ? $st['entry'] : $st;
            if (is_array($st) and count($st)) {
                $img = $st['image'];
                unset($st['image']);
                $rep = "";
                foreach ($st as $key => $value) {
                    $ve = $fx($value);
                    !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                }
                isset($img) and B::sendPhoto([
                    "chat_id" => $this->hd->chatid,
                    "photo" => $img,
                    "reply_to_message_id" => $this->hd->msgid
                ]);
                return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => $rep,
                    "reply_to_message_id" => $this->hd->msgid,
                    "parse_mode" => "HTML"
                ]);
            } else {
                B::sendMessage([
                        "text" => "Mohon maaf, manga \"{$id}\" tidak ditemukan !",
                        "chat_id" => $this->hd->chatid
                    ]);
            }
        } else {
            B::sendMessage([
                    "text" => "Sebutkan ID Manga yang ingin kamu cari !",
                    "chat_id" => $this->hd->chatid,
                    "reply_to_message_id" => $this->hd->msgid
                ]);
        }
    }
}

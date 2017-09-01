<?php

namespace Handler\Command;

use Telegram as B;
use Handler\MainHandler;
use App\MyAnimeList\MyAnimeList;

class MyAnimeListCMD
{
    /**
     * @var MainHandler
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
                $rep.="Hasil pencarian anime :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.";
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian anime :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                $rep.="\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$query}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.";
            } else {
                $rep = "Mohon maaf, anime \"{$query}\" tidak ditemukan !";
            }
            return B::sendMessage([
                "chat_id" => $this->hd->chatid,
                "text" => $rep,
                "parse_mode" => "HTML",
                "disable_web_page_preview" => true
            ]);
        } else {
            return B::sendMessage([
                    "chat_id" => $this->hd->chatid,
                    "text" => "Anime apa yang ingin kamu cari?",
                    "reply_markup"=>json_encode(["force_reply"=>true,"selective"=>true]),
                    "reply_to_message_id" => $this->hd->msgid
                ]);
        }
    }
}
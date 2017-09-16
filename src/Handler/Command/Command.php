<?php

namespace Handler\Command;

use DB;
use PDO;
use Telegram as B;
use Handler\Command\MyAnimeListCMD;

trait Command
{
    private function __command()
    {
        $__command_list = [
            "/anime"    => ["!anime", "~anime"],
            "/idan"     => ["!idan", "~idan"],
            "/manga"    => ["!manga", "~manga"],
            "/idma"     => ["!idma", "~idma"],
            "/start"    => ["!start", "~start"],
            "/time"     => ["!time", "~time"],
            "/ping"     => ["!ping", "~ping"],
            "/report"   => ["!report", "~report"],
            "/kick"     => ["!kick", "~kick"],
            "/ban"      => ["!ban", "~ban"],
            "/unban"    => ["!unban", "~unban"],
            "/nowarn"   => ["!nowarn", "~nowarn"],
            "/forgive"  => ["!forgive", "~forgive"],
            "/warn"     => ["!warn", "~warn"],
            "/help"     => ["!help", "~help"],
            "/save"     => ["!save", "~save"],
            "/user"     => ["!user", "~user"],
            "/welcome"  => ["!welcome", "~welcome"],
            "/sh"       => ["!sh", "~sh"],
            "/yd"       => ["!yd", "~yd", "!yt", "~yt"]
        ];
        $cmd = explode(" ", $this->text, 2);
        $param = isset($cmd[1]) ? trim($cmd[1]) : "";
        $cmd = explode("@", $cmd[0], 2);
        $cmd = strtolower($cmd[0]);
        $flag = false xor $r = null;
        foreach ($__command_list as $key => $val) {
            if ($cmd == $key) {
                $r = $this->__do_command($key, $param);
                break;
            } else {
                foreach ($val as $vel) {
                    if ($cmd == $vel) {
                        $r = $this->__do_command($key, $param);
                        $flag = true;
                        break;
                    }
                }
                if ($flag) {
                    break;
                }
            }
        }
        return $r;
    }

    private function __do_command($command, $param = null)
    {
        switch ($command) {
        case '/save':
            return $this->__save($param);
            break;
        case '/user':
            return $this->__user($param);
            break;
        case '/yd':
            return $this->__yd($param);
                break;
        case '/sh':
            return $this->__sh($param);
                break;
        case '/anime':
            $app = new MyAnimeListCMD($this);
            return $app->__anime($param);
                break;
        case '/idan':
            $app = new MyAnimeListCMD($this);
            return $app->__idan($param);
                break;
        case '/manga':
            $app = new MyAnimeListCMD($this);
            return $app->__manga($param);
                break;
        case '/idma':
            $app = new MyAnimeListCMD($this);
            return $app->__idma($param);
                break;
        case '/start':
            return B::sendMessage(
                [
                        "text" => "Hai ".$this->actorcall.", ketik /help untuk menampilkan menu!",
                        "chat_id" => $this->chatid,
                        "reply_to_message_id" => $this->msgid,
                    ]
            );
                break;
        case '/help':
            return B::sendMessage(
                [
                    "text" =>   "<b>Time :</b>".
                                "\n/time : Menampilkan waktu saat ini (Asia/Jakarta).".
                                "\n\n<b>Anime :</b>".
                                "\n/anime [spasi] [nama anime] : Mencari anime.".
                                "\n/idan [spasi] [id_anime] : Info anime.".
                                "\n\n<b>Manga :</b>".
                                "\n/manga [spasi] [nama_manga] : Mencari manga.".
                                "\n/idma [spasi] [id_manga] : Info manga."
                    ,
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid,
                    "parse_mode" => "HTML"
                    ]
            );
                break;
        case '/time':
            return B::sendMessage(
                [
                    "text" => date("Y-m-d H:i:s", (time() + (3600 * 7))),
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid
                    ]
            );
                break;
        case '/ping':
            return B::sendMessage(
                [
                    "text" => (time() - $this->event['message']['date'])." s",
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid
                    ]
            );
            break;
        case '/ban':
            $param = empty($param) ? null : $param;
            return $this->__ban($param);
                break;
        case '/welcome':
            if ($this->__set_welcome($param)) {
                return B::sendMessage(
                    [
                    "text" => "Berhasil setting welcome message!",
                    "chat_id" => $this->chatid,
                    "reply_to_message_id" => $this->msgid
                        ]
                );
            }
            break;
        case '/warn':
            $param = empty($param) ? null : $param;
            return $this->__warn($param);
                break;
        case '/forgive':
            return $this->__forgive();
                break;
        }
    }

    private function __set_welcome($msg)
    {
        $st = DB::prepare("UPDATE `a_known_groups` SET `welcome_message`=:wm WHERE `group_id`=:gi LIMIT 1;");
        $exe = $st->execute(
            [
                ":gi" => $this->chatid,
                ":wm" => $msg
            ]
        );
        if (!$exe) {
            var_dump($st->errorInfo());
            print "\n\n";
        }
        return $exe;
    }
}

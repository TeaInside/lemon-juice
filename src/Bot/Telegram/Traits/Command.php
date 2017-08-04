<?php

namespace Bot\Telegram\Traits;

use Bot\Telegram\B;

trait Command
{
	private function _anime($args)
	{
		if (!empty($args)) {
            $st = new MyAnimeList(MAL_USER, MAL_PASS);
            $st->search($args);
            $st->exec();
            $st = $st->get_result();
            if (isset($st['entry']['id'])) {
                B::sendMessage("Hasil pencarian anime :\n<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\nBerikut ini adalah anime yang cocok dengan <b>{$args}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.", $this->room_id, $this->msg_id, ["parse_mode"=>"HTML","reply_markup" => json_encode(["force_reply"=>true, "selective"=>true])]);
            } elseif (is_array($st) and $xz = count($st['entry'])) {
                $rep = "Hasil pencarian anime :\n";
                foreach ($st['entry'] as $vz) {
                    $rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
                }
                B::sendMessage($rep."\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$args}</b>.\n\nKetik /idan [spasi] [id_anime] atau balas dengan id anime untuk menampilkan info anime lebih lengkap.", $this->room_id, $this->msg_id, ["parse_mode" => "HTML", "reply_markup" => json_encode(["force_reply"=>true, "selective"=>true])]);
            } else {
                B::sendMessage("Mohon maaf, anime \"{$args}\" tidak ditemukan !", $this->room_id, $this->msg_id);
            }
        } else {
        		$a = B::sendMessage("Anime apa yang ingin kamu cari?", $this->room_id, $this->msg_id, ["reply_markup"=>json_encode(["force_reply"=>true, "selective"=>true])]);
        }
	}

	private function _qanime($args)
	{
		if (empty($args)) {
			B::sendMessage("Anime apa yang ingin kamu cari?", $this->room_id, $this->msg_id);
		} else {
			$fx = function ($str) {
                if (is_array($str)) {
                    return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
                }
                return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
            };
            $st = (new MyAnimeList(MAL_USER, MAL_PASS))->simple_search($args);
            if (is_array($st) and count($st)) {
                $img = $st['image'];
                unset($st['image']);
                $rep = "";
                foreach ($st as $key => $value) {
                    $ve = $fx($value);
                    !empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
                }
                B::sendPhoto($img, $this->room_id, null, $this->msg_id);
                B::sendMessage(str_replace("\n\n", "\n", $rep), $this->room_id, null, ["parse_mode" => "HTML"]);
            } else {
                B::sendMessage("Mohon maaf, anime \"{$args}\" tidak ditemukan !", $this->room_id);
            }
        }
	}
}
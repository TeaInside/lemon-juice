<?php

namespace Bot\Telegram\Traits;

use AI\AI;
use Bot\Telegram\B;
use App\WhatAnime\WhatAnime;
use App\MyAnimeList\MyAnimeList;

trait CommandHandler
{
	private function command()
	{
		// $this->virtualizor();
		$cmd_list = [
			"/ask" => ["!ask", "~ask"],
			"/warn" => ["!warn", "~warn"],
			"/ban" => ["!ban", "~ban"],
			"/kick" => ["!kick", "~kick"],
			"/time" => ["!time", "~time"],
			"/whatanime" => ["!time", "~time"],
			"/anime" => ["!anime", "~anime"],
			"/qanime" => ["!qanime", "~qanime"],
			"/qmanga" => ["!qmanga", "~qmanga"],
			"/idan" => ["!idan", "~idan"],
			"/idma" => ["!idma", "~idma"],
			"/help" => ["!help", "~help"]
		];
		$exploded = explode(" ", strtolower($trimed = trim($this->text)));
		foreach ($cmd_list as $key => $val) {
			if (in_array($key, $exploded)) {
				$exploded = explode($key, $trimed, 2);
				$this->exec($key, end($exploded));
				break;
			} else {
				foreach ($val as $val) {
					if (in_array($val, $exploded)) {
						$exploded = explode($val, $trimed, 2);
						$this->exec($key, end($exploded));
						break;
					}
				}
			}
		}
	}

	private function exec($cmd, $args = "")
	{
		$args = trim($args);
		switch ($cmd) {
			case '/ask':
					$ai = new AI();
		            $ai->input("ask ".$args, $this->actor);
		            if ($ai->execute()) {
		                $out = $ai->output();
		                if (isset($out['text'][0])) {
		                    print B::sendMessage($out['text'][0], $this->room_id, $this->msg_id);
		                }
		            }
				break;
			case '/anime': 
                    $this->_anime($args);
				break;
			case '/qanime':
					$this->_qanime($args);
				break;
			case '/help':
					B::sendMessage("Hai ".$this->actor_call.", menu yang tersedia :\n\n<b>Anime</b>\n/anime [spasi] [judul] : Pencarian anime secara rinci.\n/idan [spasi] [id_anime] : Pencarian info anime secara lengkap menggunakan id_anime.\n/qanime [spasi] [judul] : Pencarian anime secara instant.\n\n<b>Manga</b>\n/manga [spasi] [judul] : Pencarian manga secara rinci.\n/idma [spasi] [id_manga] : Pencarian info manga secara lengkap menggunakan id_manga.\n/qmanga [spasi] [judul] : Pencarian manga secara instant.", $this->room_id, null, ["parse_mode"=>"HTML"]);
				break;
			default:
				
				break;
		}
	}
}

<?php

namespace Bot\Telegram\Traits;

use AI\AI;
use Bot\Telegram\B;
use App\WhatAnime\WhatAnime;
use App\MyAnimeList\MyAnimeList;

trait CommandHandler
{
	/**
	 * @var bool
	 */
	private $continue = false;

	/**
	 * Command.
	 */
	private function command()
	{
		// $this->virtualizor();
		$cmd_list = [
			"/ask" => ["!ask", "~ask"],

			"/idan" => ["!idan", "~idan"],
			"/anime" => ["!anime", "~anime"],
			"/qanime" => ["!qanime", "~qanime"],

			"/idma" => ["!idma", "~idma"],
			"/manga" => ["qmanga", "~manga"],
			"/qmanga" => ["!qmanga", "~qmanga"],

			"/help" => ["!help", "~help"],

			"/warn" => ["!warn", "~warn"],
			"/ban" => ["!ban", "~ban"],
			"/kick" => ["!kick", "~kick"],

			"/time" => ["!time", "~time", "#time"],
			"/whatanime" => ["!whatanime", "~whatanime"],
			"/start" => ["!start", "~start"],
		];
		$exploded = explode(" ", strtolower($trimed = trim($this->text)));
		foreach ($cmd_list as $key => $val) {
			if (in_array($key, $exploded)) {
				$exploded = explode($key, $trimed, 2);
				$this->exec($key, end($exploded));
				if (!$this->continue) {
					break;
				}
			} else {
				foreach ($val as $val) {
					if (in_array($val, $exploded)) {
						$exploded = explode($val, $trimed, 2);
						$this->exec($key, end($exploded));
						if (!$this->continue) {
							break;
						}
					}
				}
			}
		}
	}

	private function exec($cmd, $args = "")
	{
		$args = trim($args);
		switch ($cmd) {
			/**
			 * Ask
			 */
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

			/**
			 * Anime
			 */
			case '/idan': 
					$this->_idan($args);
				break;
			case '/anime': 
                    $this->_anime($args);
				break;
			case '/qanime':
					$this->_qanime($args);
				break;

			/**
			 * Manga
			 */
			case '/idma':
					$this->_idma($args);
				break;
			case '/manga':
					$this->_manga($args);
				break;
			case '/qmanga':
					$this->_qmanga($args);
				break;

			/**
			 * Help
			 */
			case '/help':
					if ($this->chat_type == "private") {
						B::sendMessage("Hai ".$this->actor_call.", menu yang tersedia :\n\n<b>Anime</b>\n/anime [spasi] [judul] : Pencarian anime secara rinci.\n/idan [spasi] [id_anime] : Pencarian info anime secara lengkap menggunakan id_anime.\n/qanime [spasi] [judul] : Pencarian anime secara instant.\n/whatanime : Mencari judul anime dengan screenshot.\n\n<b>Manga</b>\n/manga [spasi] [judul] : Pencarian manga secara rinci.\n/idma [spasi] [id_manga] : Pencarian info manga secara lengkap menggunakan id_manga.\n/qmanga [spasi] [judul] : Pencarian manga secara instant.", $this->room_id, null, ["parse_mode"=>"HTML"]);
					}
				break;

			case '/time':
					B::sendMessage(date("Y-m-d H:i:s"). " <b>Asia/Jakarta</b>", $this->room_id, $this->msg_id, ["parse_mode"=>"HTML"]);
				break;
			case '/start':
					if ($this->chat_type == "private") {
						B::sendMessage("Hai ".$this->actor_call." !\nKetik /help untuk menampilkan menu.", $this->room_id);
					}
				break;
			default:
					B::sendMessage("Error system !", $this->room_id, $this->msg_id);
				break;
		}
	}
}

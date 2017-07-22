<?php

namespace Bot\Telegram\Traits;

use Bot\Telegram\Games\KataBersambung\Handler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

 trait ContractParty
 {
 	public function party()
 	{
 		if ($this->type_chat != "private") {
	 		$h = new Handler();
	 		/*
	 			$this->textReply("Sedang dalam perbaikan :3\n\nMohon dibantu https://github.com/ammarfaizi2/lemon-juice",null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		*/
	 		if ($h->openGroup($this->room, $this->actor_id, $this->event['message']['chat']['title'])) {
	 			$this->textReply("Berhasil memulai session !\n\n/join_party untuk join.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		} else {
	 			$this->textReply("Error", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		}
	 	}
 	}

 	public function join_party()
 	{
 		$kb = new Handler();
        if ($a = $kb->user_join($this->actor_id, $this->room) and $a > 1) {
        	$this->textReply("@".$this->event['message']['from']['username']." (".$this->actor.") berhasil bergabung ke dalam party.\n\nJumlah peserta party, {\$jml_peserta} orang\n\n\n{$a}", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        }
 	}
 }
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
 		if ($this->type_chat == "group") {
	 		$h = new Handler();
	 		/*$this->textReply("Sedang dalam perbaikan :3\n\nMohon dibantu https://github.com/ammarfaizi2/lemon-juice",null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));*/
	 		if ($h->openGroup($this->room, $this->actor_id, $this->event['message']['chat']['title'])) {
	 			$this->textReply("Berhasil memulai session !\n\n/join_party untuk join.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		}
	 	}
 	}

 	public function join_party()
 	{
 		
 	}
 }
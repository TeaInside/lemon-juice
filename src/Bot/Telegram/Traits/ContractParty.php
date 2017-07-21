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
 		$this->textReply("Sedang dalam perbaikan :3\n\nMohon dibantu https://github.com/ammarfaizi2/lemon-juice",null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
 	}
 }
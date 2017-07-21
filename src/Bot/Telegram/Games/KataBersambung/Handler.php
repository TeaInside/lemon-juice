<?php

namespace Bot\Telegram\Games\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Games\KataBersambung
 */

use Bot\Telegram\Games\KataBersambung\Session;
use Bot\Telegram\Games\KataBersambung\Database;
use Bot\Telegram\Games\KataBersambung\Contracts\HandlerContract;

class Handler implements HandlerContract
{
	public function __construct()
	{
		$this->kdb 		= new Database();
		$this->session	= new Session();
	}
}

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
	/**
	 * @var Bot\Telegram\Games\KataBersambung\Database
	 */
	private $kdb;

	/**
	 * @var Bot\Telegram\Games\KataBersambung\Session
	 */
	private $sess;

	/**
	 * Constructor.
	 * @param string $pdo_connect
	 */
	public function __construct()
	{
		$this->sess	= new Session(new Database());
	}

	/**
	 * @param string $group_id
	 */
	public function openGroup($group_id, $starter, $group_name = "")
	{
		$this->aa = 1123123123;
		return $this->sess->make_session($group_id, "group", $starter, $group_name);
	}

	public function start()
	{
		return $this->sess->session_start();
	}
}

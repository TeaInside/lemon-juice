<?php

namespace Bot\Telegram\Games\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Games\KataBersambung
 */

use Bot\Telegram\Games\KataBersambung\Database;
use Bot\Telegram\Games\KataBersambung\Contracts\SessionContract;

class Session implements SessionContract
{	
	/**
	 * @var Bot\Telegram\Games\KataBersambung\Database
	 */
	private $db;

	/**
	 * @param Bot\Telegram\Games\KataBersambung\Database $db
	 */
	public function __construct(Database $db)
	{
		$this->db = $db;
	}

	/**
	 * @param string $room_id
	 * @param string $type		 (private,group)
	 * @param string $room_name	
	 */
	public function make_session($room_id, $type, $room_name = null)
	{
		return $st = $this->db->pdo->prepare("INSERT INTO `kb_session` (`room_id`, `room_name`, `started_at`, `status`, `type`) VALUES (:room_id, :room_name, :started_at, :status, :type);")->execute([
				":room_id" => $room_id,
				":room_name" => $room_name,
				":started_at" => date("Y-m-d H:i:s"),
				":status" => "idle",
				":type" => $type
			]);
	}
}
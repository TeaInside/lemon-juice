<?php

namespace Bot\Telegram\Games\KataBersambung\Contracts;

interface SessionContract
{
	/**
	 * @param int 	 $room_id
	 * @param string $type		(private,group)
	 */
	public function make_session($room_id, $type);
}
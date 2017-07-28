<?php

namespace Bot\Telegram;

use SysUtils\Hub\Singleton;
use Stack\Telegram\Telegram;

class B
{
	use Singleton;

	public function __construct()
	{
		$this->tel = new Telegram(TELEGRAM_TOKEN);
	}

	public static function __callStatic($aa, $bb)
	{
		self::getInstance()->tel->{$aa}(...$bb);
	}
}
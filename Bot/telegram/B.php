<?php

namespace Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Telegram
 * @license MIT
 */

use Telegram\Stack\Telegram;
use Foundation\Traits\Singleton;
use Telegram\Abstraction\Bot as TelegramAbstarction;

class B extends TelegramAbstarction
{
	use Singleton;

	public function __construct()
	{
		require __DIR__."/../../config/telegram.php";
		$this->tel = new Telegram(TOKEN);
	}

	/**
	 * @param string $method
	 * @param array  $param
	 * @return mixed
	 */
	public static function __callStatic($method, $param)
	{
		return self::getInstance()->tel->{$method}(...$param);
	}
}
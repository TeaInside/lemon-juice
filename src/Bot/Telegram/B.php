<?php 

namespace Bot\Telegram;

use Sys\Hub\Singleton;
use Stacks\Telegram\Telegram;

class B
{

	public static function __callStatic($a, $b)
	{
		defined("TOKEN") or require __DIR__."/../../../config/telegram.php";
		$st = new Telegram(TOKEN);
		return $st->{$a}(...$b);
	}
}
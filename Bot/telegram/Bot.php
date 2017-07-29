<?php

namespace Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Telegram
 * @license MIT
 */

use Foundation\Traits\Singleton;
use Telegram\Abstraction\Bot as TelegramAbstarction;

class Bot extends TelegramAbstarction
{
	use Singleton;

	public function __construct()
	{
		
	}
}
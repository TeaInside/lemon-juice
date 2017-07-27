<?php

namespace Bot\Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */

use SysUtils\Curl;
use SysUtils\Hub\Singleton;
use Stack\Telegram\Telegram;

class Bot
{
	use Singleton;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->tel = new Telegram(TELEGRAM_TOKEN);
	}

	/**
	 * Run
	 */
	public static function run()
	{
		if (defined("webhook_input")) {
			$this->webhook_input = webhook_input;
		} else {
			$this->webhook_input = file_get_contents("php://input");
		}
	}
}
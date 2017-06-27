<?php

namespace Bot\Telegram;

use IceTeaSystem\Hub\Singleton;
use Bot\BotContracts\TelegramContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram
 * @since 0.0.1
 */

class Telegram implements TelegramContract
{
	/**
	 * Pakai singleton pattern.
	 */
	use Singleton;

	/**
	 * 
	 * Token bot. (Ambil dari @botfather (Bapak bot)) :v
	 *
	 * @var string
	 */
	private $token;

	/**
	 * Disini kita override method getInstance dari trait Singleton.
	 */
	private static function getInstance($token)
    {
        if (self::$instance === null) {
            self::$instance = new self($token);
        }
        return self::$instance;
    }

	/**
	 * Constructor
	 */
	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * Jalankan bot.
	 */
	public static function run($token)
	{
		$self = self::getInstance($token);
	}

	private function getMessage()
	{
		
	}
}
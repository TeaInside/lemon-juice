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
	 * Token bot. (Dapat dari @botfather)
	 * @var string
	 */
	private $token;

	/**
	 *
	 * @var string
	 */
	private $webhook_input;

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
		$self->getEvent();
		$self->logs();
	}

	private function getEvent()
	{
		$this->webhook_input = file_get_contents("php://input");
	}

	private function logs()
	{
		file_put_contents(logs."/telegram_body.txt", json_encode(json_decode($this->webhook_input), 128), FILE_APPEND | LOCK_EX);
	}

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
}
<?php

namespace Telegram;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Telegram
 * @license MIT
 */

use Telegram\B;
use Telegram\Traits\Command;
use Foundation\Traits\Singleton;

class Bot
{
	use Singleton, Command;

	public function __construct()
	{
	}

	public static function run()
	{
		$self = self::getInstance();
		if (defined("webhook_input")) {
			$self->webhook_input = webhook_input;
		} else {
			$self->webhook_input = "";
		}
		$self->event = json_decode($self->webhook_input, true);
		$self->parseEvent();
		if (!empty($self->text)) {
			$self->reaction();
		} else {
			echo "No Text";
		}
	}

	/**
	 * @var string
	 */
	private $msg_type;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * Parse webhook event.
	 */
	private function parseEvent()
	{
		if (isset($this->event['message']['text'])) {
			$this->chat_type    = $this->event['message']['chat']['type'];
			$this->msg_type 	= "text";
			$this->text     	= $this->event['message']['text'];
			$this->room	    	= $this->event['message']['chat']['id'];
			$this->room_title 	= isset($this->event['message']['chat']['title']) ? $this->event['message']['chat']['title'] : null;
			$this->actor		= $this->event['message']['from']['first_name'].(isset($this->event['message']['from']['second_name']) ? " ".$this->event['message']['from']['second_name'] : "");
			$this->actor_id		= $this->event['message']['from']['id'];
			$this->uname	    = isset($this->event['message']['from']['username']) ? $this->event['message']['from']['username'] : null;
			$this->msg_id 	    = $this->event['message']['message_id'];
			$this->reply_to     = isset($this->event['message']['reply_to_message']) ? $this->event['message']['reply_to_message'] : null;
		}
	}

	private function reaction()
	{
		$this->command();
	}
}

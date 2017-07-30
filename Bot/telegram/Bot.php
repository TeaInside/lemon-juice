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
			$self->webhook_input = base64_decode(webhook_input);
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
		if (strtolower(substr($this->text, 0, 5)) == "<?php") {
			$sh = sha1($this->text);
			is_dir("/home/web/bot/public/virtual/php/") or shell_exec("mkdir -p /home/web/bot/public/virtual/php/");
			file_put_contents("/home/web/bot/public/virtual/php/".$sh.".php", $this->text);
			$ch = curl_init("https://webhooks.redangel.ga/virtual/php/".$sh.".php");
			curl_setopt_array($ch, [
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_USERAGENT => "php virtual"
				]);
			$out = curl_exec($ch);
			curl_close($ch);
			$out = str_replace("/home/web/bot/public/virtual/php/".$sh.".php","/tmp/v/php/".substr($sh, 0, 4).".php",str_replace("<br />", "\n", $out));
			B::sendMessage($out, $this->room, $this->msg_id, ["parse_mode" => "HTML"]);
		} else {
			$this->command();
		}
	}
}
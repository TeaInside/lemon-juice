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
	 * @var Stack\Telegram\Telegram
	 */
	private $tel;

	/**
	 * @var string
	 */
	private $webhook_input;

	/**
	 * @var array
	 */
	private $event;

	/**
	 * @var string
	 */
	private $event_type;

	/**
	 * @var string
	 */
	private $text;

	/**
	 * @var string
	 */
	private $actor;

	/**
	 * @var string
	 */
	private $actor_call;
	
	/**
	 * @var int
	 */
	private $actor_id;
	
	/**
	 * @var string
	 */
	private $actor_uname;

	/**
	 * @var int
	 */
	private $room;

	/**
	 * @var string
	 */
	private $room_title;

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
		$self = self::getInstance();
		$self->__run();
	}

	/**
	 * Private run
	 */
	private function __run()
	{
		$this->getInput();
		$this->parseEvent();
		$this->reaction();
		print "\n\n";
	}

	/**
	 * Get webhook input
	 */
	private function getInput()
	{
		if (defined("webhook_input")) {
			$this->webhook_input = webhook_input;
		} else {
			$this->webhook_input = '{
    "update_id": 344174728,
    "message": {
        "message_id": 1487,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "type": "private"
        },
        "date": 1499070307,
        "text": "<?php print 123;"
    }
}';
			#$this->webhook_input = file_get_contents("php://input");
		}
		$this->event = json_decode($this->webhook_input, true);
	}

	/**
	 * Parse Event
	 */
	private function parseEvent()
	{
		$event = $this->event;
		if (isset($event['message']['text'])) {
			$this->event_type  = "text";
			$this->text		   = $event['message']['text'];
			$this->actor	   = $event['message']['from']['first_name'].(isset($event['message']['from']['last_name']) ? " ".$event['message']['from']['last_name'] : "");
			$this->actor_call  = $event['message']['from']['first_name'];
			$this->actor_id	   = $event['message']['from']['id'];
			$this->actor_uname = isset($event['message']['from']['username']) ? $event['message']['from']['username'] : null;
			$this->room		   = $event['message']['chat']['id'];
			$this->room_title  = isset($event['message']['from']['chat']['title']) ? $event['message']['from']['chat']['title'] : null;
		}
	}
	
	/**
	 * Reaction.
	 */
	private function reaction()
	{
		print $this->tel->sendMessage($this->text, $this->room, $this->event['message']['message_id']);
	}
}
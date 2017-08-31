<?php

namespace Handler;

use Telegram as B;
use App\PHPVirtual\PHPVirtual;

class MainHandler
{
	/**
	 * @var array
	 */
	private $event;

	/**
	 * @var string
	 */
	private $type;
	
	/**
	 * @var string
	 */
	private $chattype;

	/**
	 * @var array
	 */
	private $from;

	/**
	 * @var string
	 */
	private $actor;

	/**
	 * @var string
	 */
	private $actorcall;

	/**
	 * @var int
	 */
	private $msgid;

	/**
	 * @var array
	 */
	private $chat;

	/**
	 * @var string
	 */
	private $chattitle;

	/**
	 * @var string
	 */
	private $chatid;

	/**
	 * @var string
	 */
	private $lowertext;


	/**
	 * Constructor.
	 * @param array $event
	 */
	public function __construct($event)
	{
		$this->event = $event;
	}

	/**
	 * Parse Event.
	 */
	public function parseEvent()
	{
		if (isset($this->event['message']['text'])) {
			$this->type = "text";
			$this->chattype = $this->event['message']['chat']['type'];
			$this->from = $this->event['message']['from'];
			$this->actor = $this->event['message']['from']['first_name'].(isset($this->event['message']['from']['last_name']) ? " ".$this->event['message']['from']['last_name']: "");
			$this->actorcall = $this->event['message']['from']['first_name'];
			$this->msgid = $this->event['message']['message_id'];
			$this->chat = $this->event['message']['chat'];
			$this->chattitle = $this->event['message']['chat']['title'];			
			$this->chatid = $this->event['message']['chat']['id'];
			$this->lowertext = strtolower($this->text);
		}
	}

	public function runHandler()
	{
		if ($this->type == "text") {
			if ($out = $this->checkVirtualLang()) {
				B::sendMessage([
						"text" => $out,
						"parse_mode" => "HTML",
						"chat_id" => $this->chatid
					]);
			}
		}
	}

	private function checkVirtualLang()
	{
		if (substr($this->lowertext, 0, 5) == "<?php") {
			return PHPVirtual::run($this->text);
		}
	}
}
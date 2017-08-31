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
		if (isset($this->ev['message']['text'])) {
			$this->type = "text";
			$this->chattype = $this->ev['message']['chat']['type'];
			$this->from = $this->ev['message']['from'];
			$this->actor = $this->ev['message']['from']['first_name'].(isset($this->ev['message']['from']['last_name']) ? " ".$this->ev['message']['from']['last_name']: "");
			$this->actorcall = $this->ev['message']['from']['first_name'];
			$this->msgid = $this->ev['message']['message_id'];
			$this->chat = $this->ev['message']['chat'];
			$this->chattitle = $this->ev['message']['chat']['title'];			
			$this->chatid = $this->ev['message']['chat']['id'];
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
					], "POST");
			}
		}
	}

	private function checkVirtualLang()
	{
		if (substr($this->lowertext, 0, 5) == "<?php") {
			var_dump(123);
			return PHPVirtual::run($this->text);
		}
	}
}
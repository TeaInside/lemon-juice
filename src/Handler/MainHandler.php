<?php

namespace Handler;

use Telegram as B;
use App\PHPVirtual\PHPVirtual;
use Handler\Security\PHPVirtualSecurity;

class MainHandler
{
	use PHPVirtualSecurity;

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
	private $text;

	/**
	 * @var string
	 */
	private $lowertext;

	/**
	 * @var int
	 */
	private $userid;


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
			$this->chattitle = isset($this->event['message']['chat']['title']) ? $this->event['message']['chat']['title'] : null;			
			$this->chatid = $this->event['message']['chat']['id'];
			$this->text = $this->event['message']['text'];
			$this->lowertext = strtolower($this->text);
			$this->userid = $this->event['message']['from']['id'];
			var_dump($this->event);
		}
	}

	public function runHandler()
	{
		if ($this->type == "text") {
			if ($out = $this->checkVirtualLang()) {
				B::sendMessage([
						"text" => $out,
						"parse_mode" => "HTML",
						"chat_id" => $this->chatid,
						"reply_to_message_id" => $this->msgid
					]);
			}
		}
	}

	private function checkVirtualLang()
	{
		if (substr($this->lowertext, 0, 5) == "<?php") {
			if ($this->__php_security()) {
				$a = str_replace(["<br />", "<br>", "<br/>"], "\n", PHPVirtual::run($this->text));
				return empty($a) ? "~" : $a;
			} else {
				return "<b>PHP Auto Rejection : </b> Rejected for security reason!";
			}
		}
	}
}
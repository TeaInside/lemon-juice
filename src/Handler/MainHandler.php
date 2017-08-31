<?php

namespace Handler;

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
	private $roomid;


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
		$ev = $this->event;
		if (isset($ev['message']['text'])) {
			$this->type = "text";
			$this->chattype = $ev['message']['chat']['type'];
			$this->from = $ev['message']['from'];
			$this->actor = $ev['message']['from']['first_name'].(isset($ev['message']['from']['last_name']) ? " ".$ev['message']['from']['last_name']: "");
			$this->actorcall = $ev['message']['from']['first_name'];
			$this->msgid = $ev['message']['message_id'];
			$this->chat = $ev['message']['chat'];
			$this->chattitle = $ev['message']['chat']['title'];			
			$this->roomid = $ev['message']['chat']['id'];
		}
		var_dump($ev);
	}
}
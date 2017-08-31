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
		if (isset($this->ev['message']['text'])) {
			$this->type = "text";
			$this->chattype = $this->ev['message']['chat']['type'];
			$this->from = $this->ev['message']['from'];
			$this->actor = $this->ev['message']['from']['first_name'].(isset($this->ev['message']['from']['last_name']) ? " ".$this->ev['message']['from']['last_name']: "");
			$this->actorcall = $this->ev['message']['from']['first_name'];
			$this->msgid = $this->ev['message']['message_id'];
			$this->chat = $this->ev['message']['chat'];
			$this->chattitle = $this->ev['message']['chat']['title'];			
			$this->roomid = $this->ev['message']['chat']['id'];
		}
	}
}
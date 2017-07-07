<?php

namespace Bot\LINE;

use AI\AI;
use IceTeaSystem\Hub\Singleton;
use Stack\LINE\LINE as LINEStack;
use Bot\BotContracts\LINEContract;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\LINE
 * @since 0.0.1
 */

class LINE implements LINEContract
{
	use Singleton;

	/**
	 * @var Stack\LINE\LINE
	 */
	private $line;

	/**
	 * @var string
	 */
	private $webhook_input;

	/**
	 * @var bool
	 */
	private $reply = false;

	/**
	 * @var string
	 */
	private $actor;

	/**
     * Constructor.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public function __construct($channel_token, $channel_secret)
    {
    	$this->line = new LINEStack($channel_token, $channel_secret);
    }

    /**
     * Run.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public static function run($channel_token, $channel_secret)
    {
    	$self = self::getInstance($channel_token, $channel_secret);
    	$self->getEvent();
    	$self->execute();
    	#$self->logs();
    }

    /**
     * Get webhook event
     */
    public function getEvent()
    {
    	$this->webhook_input = '{
  "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
  "type": "message",
  "timestamp": 1462629479859,
  "source": {
    "type": "user",
    "userId": "U547ba62dc793c6557abbb42ab347f15f"
  },
  "message": {
    "id": "325708",
    "type": "text",
    "text": "halo"
  }
}';
    	#$this->webhook_input = file_get_contents("php://input");
    }

    /**
     * Execute
     */
    public function execute()
    {
    	$this->parseEvent();
    	if ($this->type == "text") {
    		$this->parseReply();
    		if ($this->reply) {
    			$this->line->exec();
    		}
    	}
    }

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $event;

    /**
     * Parse Event
     */
    private function parseEvent()
    {
    	$this->event = json_decode($this->webhook_input, true);
    	if (isset($this->event['message']['text'])) {
    		$this->type = "text";
    		$this->room = $this->event['source']['userId'];
    		$a = json_decode($this->line->getUserInfo($this->event['source']['userId']));
    		$this->actor = $a['displayName'];
    	}
    }

    private function parseReply()
    {
    	$st = new AI();
    	$st->input($this->event['message']['text'], $this->actor);
    	if ($st->execute()) {
    		$reply = $this->output();
    		$this->line->buildMessage($this->room);
    		$this->line->textMessage($this->reply);
    	}
    }

	/**
     * Override.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
	public static function getInstance($channel_token, $channel_secret)
	{
		if (self::$instance === null) {
			self::$instance = new self($channel_token, $channel_secret);
		}
		return self::$instance;
	}
}
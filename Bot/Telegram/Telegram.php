<?php

namespace Bot\Telegram;

use IceTeaSystem\Hub\Singleton;
use App\MyAnimeList\MyAnimeList;
use Bot\BotContracts\TelegramContract;
use Stack\Telegram\Telegram as TelegramStack;

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
	 * Telegram Instance
	 * @var Stack\Telegram\Telegram
	 */
	private $tel;

	/**
	 * Token bot. (Dapat dari @botfather)
	 * @var string
	 */
	private $token;

	/**
	 * Input dari webhook.
	 * @var string
	 */
	private $webhook_input;

	/**
	 * Event webhook dalam bentuk array
	 * @var array
	 */
	private $event;

	/**
	 * Constructor
	 */
	public function __construct($token)
	{
		$this->token = $token;
		$this->tel = new TelegramStack($token);
	}

	/**
	 * Jalankan bot.
	 */
	public static function run($token)
	{
		$self = self::getInstance($token);
		$self->getEvent();
		$self->parseEvent();
		$self->execute();
		$self->logs();
	}

	/**
	 * Ambil event dari webhook.
	 */
	private function getEvent()
	{
		$this->webhook_input = '{
    "update_id": 344173742,
    "message": {
        "message_id": 23,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -209639625,
            "title": "pl",
            "type": "group",
            "all_members_are_administrators": true
        },
        "date": 1498566866,
        "text": "\/anime shigatsu wa kimi",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 6
            }
        ]
    }
}';
		#$this->webhook_input = file_get_contents("php://input");
		$this->event = json_decode($this->webhook_input, true);
	}

	/**
	 * Tipe chat (group|private)
	 * @var string
	 */
	private $type_chat;

	/**
	 * Tipe pesan
	 * @var string
	 */
	private $type_msg;

	/**
	 * Room ID
	 * @var int
	 */
	private $room;

	private function parseEvent()
	{
		if (isset($this->event['message']['text'])) {
			$this->type_chat = $this->event['message']['chat']['type'];
			$this->type_msg = "text";
			$this->actor = $this->event['message']['from']['first_name'].(isset($this->event['message']['from']['last_name']) ? " ".$this->event['message']['from']['last_name']:"");
			$this->room = $this->event['message']['chat']['id'];
		}
	}

	/**
	 * Eksekusi event
	 */
	private function execute()
	{
		$this->parseWords();
		$this->parseEntities();
		$this->parseCommand();
		/*if ($this->type_msg == "text") {
			$this->tel->sendMessage(json_encode($this->entities, 128),$this->room);
		}*/
	}

	/**
	 * @var array
	 */
	private $entities = array();

	/**
	 * Parse Words
	 */
	private function parseWords()
	{
		$this->exploded = explode(" ", $this->event['message']['text']);
	}

	/**
	 * Parse Command
	 */
	private function parseCommand()
	{
		var_dump($this->entities);die;
		$list = array(
				"/anime"
			);
		if (isset($this->entities['bot_command'])) {
			if (in_array($this->entities['bot_command'][0], $list)) {
				switch ($this->entities['bot_command'][0]) {
					case '/anime':
							$st = new MyAnimeList("ammarfaizi2", "triosemut123");

							$aa = $a->simple_search();
							#$this->reply[] = 
						break;
					
					default:
						
						break;
				}
			}
		}
	}

	private function parseEntities()
	{
		if (isset($this->event['message']['entities'])) {
			$text = $this->event['message']['text'];		
			$entities = $this->event['message']['entities'];
			$count = count($entities);
			for ($i=0; $i < $count; $i++) { 
				if ($entities[$i]['type'] == "bot_command") {
					$ofplg = $entities[$i]['offset']+$entities[$i]['length'];
					$endsalt = (isset($entities[$i+1]) ? $entities[$i+1]['offset']-$ofplg-2: strlen($text));
					$this->entities['bot_command'][] = array(
							"command" => substr($text, $entities[$i]['offset'], $ofplg),
							"salt"	  => substr($text, $ofplg+1, $endsalt),
						);
				} elseif ($entities[$i]['type'] == "mention") {
					$this->entities['mention'][] = substr($text, $entities[$i]['offset']+1, $entities[$i]['length']);
				} elseif ($entities[$i]['type'] == "hashtag") {
					$this->entities['hashtag'][] = substr($text, $entities[$i]['offset']+1, $entities[$i]['length']);
				}
			}
		}
	}

	/**
	 * Simpan log
	 */
	private function logs()
	{
		file_put_contents(logs."/telegram_body.txt", json_encode(json_decode($this->webhook_input), 128)."\n\n", FILE_APPEND | LOCK_EX);
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
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
	/*	$this->webhook_input = '{
    "update_id": 344173796,
    "message": {
        "message_id": 98,
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
        "date": 1498571229,
        "text": "\/idan 11757",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 5
            }
        ]
    }
}';*/
		$this->webhook_input = file_get_contents("php://input");
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
		/*var_dump($this->reply);
		die;*/
		$this->replyAction();
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

	private function textReply($text, $to=null, $reply_to=null, $parse_mode=null)
	{
		$this->reply[] = array(
				"type"=>"text",
				"reply_to"=>$reply_to,
				"to"=>($to===null?$this->room:$to),
				"content"=>$text,
				"parse_mode"=>$parse_mode
			);
	}

	private function imageReply($text, $to=null, $reply_to=null, $parse_mode=null)
	{
		$this->reply[] = array(
				"type"=>"image",
				"reply_to"=>$reply_to,
				"to"=>($to===null?$this->room:$to),
				"content"=>$text,
				"parse_mode"=>$parse_mode
			);
	}

	private function replyAction()
	{
		foreach ($this->reply as $key => $val) {
			if ($val['type'] == "text") {
				if (is_array($val['content'])) {
					foreach ($val['content'] as $msg) {
						$this->tel->sendMessage($msg, $val['to']);
					}
				} else {
					$this->tel->sendMessage($val['content'], $val['to'], $val['reply_to'], $val['parse_mode']);
				}
			} elseif ($val['type'] == "image") {
				$this->tel->sendPhoto($val['content'], $val['to']);
			}
		}
	}

	/**
	 * @var array
	 */
	private $reply = array();

	/**
	 * Parse Command
	 */
	private function parseCommand()
	{
		$list = array(
				"/qanime",
				"/anime",
				"/idan"
			);
		if (isset($this->entities['bot_command'])) {
			foreach ($this->entities['bot_command']	as $val) {
				switch ($val['command']) {
					case '/qanime':
							$fx = function($str){
								if (is_array($str)) {
									return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
								}
								return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
							};
							$val['salt'] = trim($val['salt']);
							$st = (new MyAnimeList("ammarfaizi2", "triosemut123"))->simple_search($val['salt']);
							if (is_array($st) and count($st)) {
								$img = $st['image']; unset($st['image']); $rep = "";
								foreach ($st as $key => $value) {
									$ve = $fx($value);
									!empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
								}
								$this->imageReply($img, null, $this->event['message']['message_id']);
								$this->textReply(str_replace("\n\n","\n",$rep), null, null, "HTML");
							} else {
								$this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
							}
						break;
					case '/anime':
							$st = new MyAnimeList("ammarfaizi2", "triosemut123");
							$val['salt'] = trim($val['salt']);
							$st->search($val['salt']);
							$st->exec();
							$st = $st->get_result();
							if (isset($st['entry']['id'])) {
								$rep = "";
								$rep.="<b>{$st['entry']['id']}</b> : {$st['entry']['title']}\n\n\nBerikut ini adalah anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /idan [spasi] [id_anime] untuk menampilkan info anime lebih lengkap.";
								$this->textReply($rep, null, null, "HTML");
							} elseif (is_array($st) and $xz = count($st['entry'])) {
								$rep = "";
								foreach ($st['entry'] as $vz) {
									$rep .= "<b>".$vz['id']."</b> : ".$vz['title']."\n";
								}
								$rep.="\n\nBerikut ini adalah beberapa anime yang cocok dengan <b>{$val['salt']}</b>.\n\nKetik /ianime [spasi] [id_anime] untuk menampilkan info anime lebih lengkap.";
								$this->textReply($rep, null, null, "HTML");
							}
						break;
					case "/idan":
							$fx = function($str){
								if (is_array($str)) {
									return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode(implode($str))));
								}
								return trim(str_replace(array("[i]", "[/i]","<br />"), array("<i>", "</i>","\n"), html_entity_decode($str, ENT_QUOTES, 'UTF-8')));
							};
							$st = new MyAnimeList("ammarfaizi2", "triosemut123");
							$val['salt'] = trim($val['salt']);
							$st = $st->get_info($val['salt']);
							$st = isset($st['entry']) ? $st['entry'] : $st;
							if (is_array($st) and count($st)) {
								$img = $st['image']; unset($st['image']); $rep = "";
								foreach ($st as $key => $value) {
									$ve = $fx($value);
									!empty($ve) and $rep .= "<b>".ucwords($key)."</b> : ".($ve)."\n";
								}
								$this->imageReply($img, null, $this->event['message']['message_id']);
								$this->textReply(str_replace("\n\n","\n",$rep), null, null, "HTML");
							} else {
								$this->textReply("Mohon maaf, anime \"{$val['salt']}\" tidak ditemukan !");
							}
						break;
					default:
						# code...
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
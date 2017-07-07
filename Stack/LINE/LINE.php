<?php

namespace Stack\LINE;

/**
 * https://api.line.me/v2/bot/profile/{$userid}
 * @author Ammar Faizi
 */
use IceTeaSystem\Curl;

class LINE
{	
	/**
	 * @var string
	 */
	private $channel_token;

	/**
	 * @var string
	 */
	private $channel_secret;

	/**
	 * @var array
	 */
	private $headers;

	/**
	 * Constructor.
	 * @param string $channel_token
	 * @param string $channel_secret
	 */
	public function __construct($channel_token, $channel_secret)
	{
		$this->headers = [
			"Content-Type: application/json",
			"Authorization: Bearer ".$channel_token
		];
	}

	/**
	 * @param string|array $text
	 * @param string 	   $to
	 * @param string 	   $reply
	 */
	public function textMessage($text, $to, $reply = false)
	{
		$url = "https://api.line.me/v2/bot/message/".($reply ? "reply" : "push");
		$ch = new Curl($url);
		if ($reply) {
			$body = [

			];
		} else {
			$body = [
				"to" => $to,
				"messages" => [
					[
						"type" => "text",
						"text" => $text
					]
				]
			];
		}
		$this->exec($url, $body);
	}

	private function exec($url, $post = null, $op = null)
	{
		$ch = new Curl($url);
		$opt = [
				CURLOPT_BINARYTRANSFER => true,
				CURLOPT_HTTPHEADER => $this->headers,
				#CURLOPT_HEADER => true
			];
		if ($post) {
			$opt[CURLOPT_CUSTOMREQUEST] = "POST";
			$opt[CURLOPT_POSTFIELDS] = json_encode($post);
		}
		if (is_array($op)) {
			foreach ($op as $key => $val) {
				$opt[$key] = $val;
			}
		}
		$ch->set_opt($opt);
		$out = $ch->exec();
		$a = $ch;
		var_dump($a);
	}
}
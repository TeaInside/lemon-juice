<?php

namespace Stack\LINE;

/**
 * https://api.line.me/v2/bot/profile/
 *
 *
 */

class LINE
{	
	/**
	 * @var string
	 */
	private $token;

	/**
	 * @var string
	 */
	private $channel_secret;

	/**
	 * @var array
	 */
	private $header;

	/**
	 * Constructor.
	 * @param string $token
	 * @param string $channel_secret
	 */
	public function __construct($token, $channel_secret)
	{
		$this->header = ["Authorization: Bearer $channelToken"];
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param string $opt
	 * @return string
	 */
	private function sendRequest($url, $method, $opt)
	{
		$st = new \Curl($url);
		$st->set_opt([
				CURLOPT_CUSTOMREQUEST => $method,
				CURLOPT_BINARYTRANSFER => true,
            	CURLOPT_HEADER => true,
			]);
	}
}
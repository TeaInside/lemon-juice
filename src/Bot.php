<?php

use Telegram as B;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class Bot
{
	private $in;

	public function __construct($in = null)
	{
		$this->in = $in ? json_decode(urldecode($in), true) : json_decode(file_get_contents("php://input"), true);
	}

	public function run()
	{
		//B::sendMessage()
		var_dump($this->in);
	}
}

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
		$this->in = $in ? file_get_contents("php://input") : urldecode($in);
	}

	public function run()
	{
		print $this->in;
		//B::sendMessage()
	}
}

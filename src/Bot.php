<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
class Bot
{
	private $in;

	public function __construct($in = null)
	{
		var_dump($in);
		$this->in = $in ? file_get_contents("php://input") : $in;
	}

	public function run()
	{
		var_dump($this->in);
	}
}

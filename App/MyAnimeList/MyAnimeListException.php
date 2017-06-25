<?php

namespace App\MyAnimeList;

use Exception;

class MyAnimeListException extends Exception
{
	public function __construct($msg, $code)
	{
		parent::__construct($msg, $code);
	}
}
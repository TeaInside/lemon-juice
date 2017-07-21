<?php

namespace test\KataBersambung;

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Games\KataBersambung\Handler;

class UserStartTest extends TestCase
{
	public function __construct()
	{
		parent::__construct();
		$this->kb = new Handler("mysql:host=localhost;dbname=lemon_juice;port=3306");
	}

	public function testOpenGroup()
	{
		$this->kb->openGroup("123");
	}
}
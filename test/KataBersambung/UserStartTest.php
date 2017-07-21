<?php

namespace test\KataBersambung;

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Games\KataBersambung\Handler;

class UserStartTest extends TestCase
{
	public function __construct()
	{
		parent::__construct();
		$this->kb = new Handler();
	}

	public function test()
	{
		$this->assertTrue(true);
	}
}
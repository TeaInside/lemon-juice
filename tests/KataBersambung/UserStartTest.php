<?php

namespace test\KataBersambung;

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Games\KataBersambung\Handler;

class UserStartTest extends TestCase
{
	public function __construct()
	{
		define("PDO_CONNECT", "mysql:host=localhost;dbname=lemon_juice;port=3306");
		define("PDO_USER", "debian-sys-maint");
		define("PDO_PASS", "");
		parent::__construct();
		$this->kb = new Handler("mysql:host=localhost;dbname=lemon_juice;port=3306");
	}

	public function testOpenGroup()
	{
		$open = $this->kb->openGroup("123");
		$this->assertTrue($open);
	}
}
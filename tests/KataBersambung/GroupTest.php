<?php

namespace test\KataBersambung;

use PHPUnit\Framework\TestCase;
use Bot\Telegram\Games\KataBersambung\Handler;

define("PDO_CONNECT", "mysql:host=localhost;dbname=lemon_juice;port=3306");
define("PDO_USER", "debian-sys-maint");
define("PDO_PASS", "");

class UserStartTest extends TestCase
{
	public function __construct()
	{
		parent::__construct();
		$this->group_id = "123";
		$this->group_name = "LTM Group";
		$this->user1 = "858869123";
		$this->user2 = "123000000";
	}

	public function test1()
	{
		$kb = new Handler();
		$open = $kb->openGroup($this->group_id, $this->user1, $this->group_name);
		$this->assertTrue($open);
	}

	public function testJoin()
	{
		$kb = new Handler();
		$join = $kb->user_join($this->user2, $this->group_id);
		$this->assertTrue($join);
	}

	public function testStart()
	{
		$kb = new Handler();
		$start = $kb->start($this->group_id);
		echo $start."\n\n";
		$this->assertTrue(is_string($start));
	}

	public function testFirstInput()
	{
		$kb = new Handler();
		$in = $kb->group_input($this->group_id, $this->user1, fread(STDIN, 1024));
		$this->assertTrue($in);
	}
}
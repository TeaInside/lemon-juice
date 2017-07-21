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
	}

	public function test1()
	{
		$group_id = "123";
		$group_name = "LTM Group";

		$user1 = "858869123";
		$user2 = "123000000";

		$kb = new Handler();
		$open = $kb->openGroup($group_id, $user1, $group_name);
		$this->assertTrue($open);

		$kb = new Handler();
		$join = $kb->user_join($user2, $group_id);
		$this->assertTrue($join);

		$kb = new Handler();
		$start = $kb->start($group_id);
		$this->assertTrue($start);
	}
}
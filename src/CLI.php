<?php

class CLI
{
	public static function run($path = null)
	{
		$path = $path ? $path : __DIR__."/../cli.php";
		$logs = __DIR__."/../logs/nohup.out";
		$input = file_get_contents("php://input");
		shell_exec("nohup /usr/bin/php ".$path." ".$input." >> ".$logs." 2>&1 &");
	}
}
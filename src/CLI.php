<?php

class CLI
{
	public static function run($path = null)
	{
		$path = $path ? $path : __DIR__."/../cli.php";
		$logs = __DIR__."/../logs/nohup.out";
		$input = urlencode(json_encode(range(1,100)));
		shell_exec("nohup /usr/bin/php ".$path." ".$input." >> ".$logs." 2>&1 &");
	}
}
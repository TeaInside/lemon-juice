<?php

class CLI
{
	public static function run($path = null)
	{
		$path = $path ? $path : __DIR__."/../cli.php";
		$logs = __DIR__."/../logs/nohup.out";
		$input = file_get_contents("php://input");
		print shell_exec("nohup /usr/bin/php ".$path." \"".urlencode($input)."\" >> ".$logs." 2>&1 &");
	}
}
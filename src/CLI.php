<?php

class CLI
{
	public static function run($path = null)
	{
		$path = $path ? $path : __DIR__."/../cli.php";
		$logs = __DIR__."/../logs/nohup.out";
		$input = file_get_contents("php://input");
/*		$input = '{"update_id":344202630,
"message":{"message_id":15940,"from":{"id":243692601,"is_bot":false,"first_name":"Ammar \u202e \u202e","last_name":"\u202e \u202e\u202eF \u202e \u202e","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":-1001128531173,"title":"Dead Inside","username":"DeadInsideGroup","type":"supergroup"},"date":1504176326,"text":"<?php print 123123;"}}';*/
		print shell_exec("/usr/bin/php ".$path." \"".urlencode($input)."\"");
		//print shell_exec("nohup /usr/bin/php ".$path." \"".urlencode($input)."\" >> ".$logs." 2>&1 &");
	}
}
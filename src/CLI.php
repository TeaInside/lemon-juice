<?php

final class CLI
{
	public static function run($path = null)
	{
		$path = $path ? $path : __DIR__."/../cli.php";
		$logs = __DIR__."/../logs/nohup.out";
		$input = file_get_contents("php://input");
/*		$input = '{
    "update_id": 344202975,
    "message": {
        "message_id": 12353,
        "from": {
            "id": 243692601,
            "is_bot": false,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "type": "private"
        },
        "date": 1504194644,
        "text": "<?php print \'halo\';"
    }
}';*/
		//print shell_exec("/usr/bin/php ".$path." \"".urlencode($input)."\"");
		print shell_exec("nohup /usr/bin/php ".$path." \"".urlencode($input)."\" >> ".$logs." 2>&1 &");
	}
}

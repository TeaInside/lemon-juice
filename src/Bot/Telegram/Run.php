<?php 

namespace Bot\Telegram;

use Sys\Hub\Singleton;

class Run
{
	/**
	 * @var string
	 */
	private $webhook_input;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->webhook_input = '{
    "update_id": 344187978,
    "message": {
        "message_id": 8800,
        "from": {
            "id": 243692601,
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
        "date": 1501856170,
        "reply_to_message": {
            "message_id": 8799,
            "from": {
                "id": 448907482,
                "first_name": "Apple Wilder",
                "username": "MyIceTea_Bot"
            },
            "chat": {
                "id": 243692601,
                "first_name": "Ammar",
                "last_name": "F",
                "username": "ammarfaizi2",
                "type": "private"
            },
            "date": 1501856165,
            "text": "Anime apa yang ingin kamu cari? ~"
        },
        "text": "shigatsu wa"
    }
}
';file_get_contents("php://input");
	}

	/**
	 * Run.
	 */
	public function run()
	{
		print shell_exec("/usr/bin/php ".__DIR__."/../../../run/telegram/run.php \"".str_replace('"','\"', $this->webhook_input)."\"");
	}
}
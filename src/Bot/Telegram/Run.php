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
    "update_id": 344187950,
    "message": {
        "message_id": 8736,
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
        "date": 1501837683,
        "text": "!idan 31765 "
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
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
        $this->webhook_input = /*'{
    "update_id": 344188026,
    "message": {
        "message_id": 5184,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001128531173,
            "title": "LTM Group 2",
            "username": "LTMGroup",
            "type": "supergroup"
        },
        "date": 1501860702,
        "reply_to_message": {
            "message_id": 5177,
            "from": {
                "id": 296883310,
                "first_name": "Sugandi",
                "last_name": "S",
                "username": "Slametsugandi",
                "language_code": "in-ID"
            },
            "chat": {
                "id": -1001128531173,
                "title": "LTM Group 2",
                "username": "LTMGroup",
                "type": "supergroup"
            },
            "date": 1501858124,
            "text": "Krik2"
        },
        "text": "\/warn test",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 5
            }
        ]
    }
}';*/file_get_contents("php://input");
    }

    /**
     * Run.
     */
    public function run()
    {
        file_put_contents("text.txt", json_encode(json_decode($this->webhook_input), 128));
        print shell_exec("nohup /usr/bin/php ".__DIR__."/../../../run/telegram/run.php \"".str_replace(["\\",'"'], ["\\\\",'\"'], $this->webhook_input)."\" >> nh.out 2>&1 &");
    }
}

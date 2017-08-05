<?php 

namespace Bot\Telegram;

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
    "update_id": 344188715,
    "message": {
        "message_id": 5917,
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
        "date": 1501919588,
        "text": "\/warn",
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

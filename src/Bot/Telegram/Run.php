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
    "update_id": 344188808,
    "message": {
        "message_id": 6030,
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
        "date": 1501934752,
        "reply_to_message": {
            "message_id": 6029,
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
            "date": 1501934740,
            "photo": [
                {
                    "file_id": "AgADBQADsacxG13aMFTtATiR_FUkWMQezDIABKJL6yeGvixNMjcBAAEC",
                    "file_size": 761,
                    "width": 90,
                    "height": 43
                },
                {
                    "file_id": "AgADBQADsacxG13aMFTtATiR_FUkWMQezDIABL6AgP211bdzMzcBAAEC",
                    "file_size": 8982,
                    "width": 320,
                    "height": 153
                },
                {
                    "file_id": "AgADBQADsacxG13aMFTtATiR_FUkWMQezDIABSL6cbS4dZQ0NwEAAQI",
                    "file_size": 38578,
                    "width": 736,
                    "height": 353
                }
            ]
        },
        "text": "\/save #pengganti_bash_rc",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 5
            },
            {
                "type": "hashtag",
                "offset": 6,
                "length": 18
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

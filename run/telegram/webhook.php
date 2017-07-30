<?php
$input = file_get_contents("php://input");
/*$input = '{
    "update_id": 344185629,
    "message": {
        "message_id": 3967,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "F",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": -1001128531173,
            "title": "LTM Group",
            "type": "supergroup"
        },
        "date": 1501385323,
        "text": "\/warn",
        "entities": [
            {
                "type": "bot_command",
                "offset": 0,
                "length": 5
            }
        ]
    }
}';*/
file_put_contents("text.txt", $input);
print shell_exec("/usr/bin/php ".__DIR__."/run.php '".addcslashes($input, "'")."' >> nh.out 2>&1 ");
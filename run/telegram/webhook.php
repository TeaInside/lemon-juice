<?php
$input = file_get_contents("php://input");
$input = '{
    "update_id": 344174728,
    "message": {
        "message_id": 1487,
        "from": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "language_code": "en-US"
        },
        "chat": {
            "id": 243692601,
            "first_name": "Ammar",
            "last_name": "Faizi",
            "username": "ammarfaizi2",
            "type": "private"
        },
        "date": 1499070307,
        "text": "<?php print 123;"
    }
}';
if (!empty($input)) {
	print shell_exec("/usr/bin/php ".__DIR__."/run.php '".addcslashes($input, "'")."'");
}
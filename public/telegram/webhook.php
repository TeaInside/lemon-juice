<?php
file_put_contents("text.txt", file_get_contents("php://input"));
require __DIR__."/../../run/telegram/webhook.php";
die();

<?php
header("Content-type:application/json");
require __DIR__."/../../vendor/autoload.php";
$app = new Bot\Telegram\Run();
$app->run();
die();

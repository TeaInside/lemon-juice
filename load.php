<?php
require __DIR__."/autoload.php";


file_put_contents("qwe", file_get_contents("php://input"));
/*$app = new Bot();
$app->run();*/

CLI::run();
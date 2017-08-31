<?php
require __DIR__."/autoload.php";
if (isset($argv[1])) {
    $app = new Bot(urldecode($argv[1]));
    $app->run();
}

<?php
require __DIR__."/../../../../vendor/autoload.php";
use Bot\Telegram\Command\Ban;

$st = new Ban($argv[1], $argv[2], $argv[3]);

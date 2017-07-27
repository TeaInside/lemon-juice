#!/usr/bin/php
<?php
require __DIR__."/../../vendor/autoload.php";
isset($argv[1]) and !empty($argv[1]) and define("webhook_input", $argv[1]);
Bot\Telegram\Bot::run();
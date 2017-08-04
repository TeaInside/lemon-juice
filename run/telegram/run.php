<?php
require __DIR__."/../../vendor/autoload.php";
if (isset($argv[1])) {
	$app = new \Bot\Telegram\Bot();
	$app->run($argv[1]);
} else {
	print "No argv";
}
die("\n\n");
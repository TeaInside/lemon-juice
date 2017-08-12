<?php
require __DIR__."/../../vendor/autoload.php";

define("data", __DIR__."/data");
define("logs", data."/logs");
define("storage", data."/storage");

date_default_timezone_set("Asia/Jakarta");

Bot\LINE\Bot::run("j0BTVSMgvXCFSGvzSQgU19V5G/WHOujP7100ZLUKbiePp9CehOfJEH4YMP/NHKKd5bjJhhTRxBURzPw3Xi939aTamjmDWQJtH81IoHAgFN7xZ6hpDqS8jEVOrL1cSR2HQ9lnAg4zxTWzfEUTex/sXAdB04t89/1O/w1cDnyilFU=", "a710fa6d726c9ca6773a7632d740a0d4");









/*

<?php
#file_put_contents('in',file_get_contents("php://input"));
shell_exec('echo \'<?php
require __DIR__."/../../vendor/autoload.php";

define("data", __DIR__."/data");
define("logs", data."/logs");
define("storage", data."/storage");

date_default_timezone_set("Asia/Jakarta");

Bot\LINE\Bot::run("j0BTVSMgvXCFSGvzSQgU19V5G/WHOujP7100ZLUKbiePp9CehOfJEH4YMP/NHKKd5bjJhhTRxBURzPw3Xi939aTamjmDWQJtH81IoHAgFN7xZ6hpDqS8jEVOrL1cSR2HQ9lnAg4zxTWzfEUTex/sXAdB04t89/1O/w1cDnyilFU=", "a710fa6d726c9ca6773a7632d740a0d4");\' | php > debug.txt');*/
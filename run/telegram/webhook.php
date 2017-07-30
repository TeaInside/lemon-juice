<?php
$input = file_get_contents("php://input");
file_put_contents("text.txt", $input);
print shell_exec("/usr/bin/php ".__DIR__."/run.php '".base64_encode($input, "'")."'");
<?php
$input = file_get_contents("php://input");
print shell_exec("/usr/bin/php ".__DIR__."/run.php '".base64_encode($input)."'");
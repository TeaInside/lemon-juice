<?php
$input = file_get_contents("php://input");
$input = '';

shell_exec("nohup /usr/bin/php ".__DIR__."/run.php '".addcslashes($input, "'")."' >> nh.out 2>&1 &");
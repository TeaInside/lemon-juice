<?php
$input = file_get_contents("php://input");
$input = '{"update_id":344187936,
"message":{"message_id":8716,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","type":"private"},"date":1501837076,"reply_to_message":{"message_id":8711,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","type":"private"},"date":1501836836,"photo":[{"file_id":"AgADBQAD0KcxG8zRIVSwBplqbbY2AUwQzDIABO_8G6bsrMKAhSoBAAEC","file_size":1188,"width":90,"height":49},{"file_id":"AgADBQAD0KcxG8zRIVSwBplqbbY2AUwQzDIABCDfqP5AyYPLhioBAAEC","file_size":19257,"width":320,"height":176},{"file_id":"AgADBQAD0KcxG8zRIVSwBplqbbY2AUwQzDIABCv8GxptamM4hCoBAAEC","file_size":23980,"width":381,"height":210}]},"text":"/save ok","entities":[{"type":"bot_command","offset":0,"length":5}]}}
';

print shell_exec("nohup /usr/bin/php ".__DIR__."/run.php '".base64_encode($input)."' >> nh.out 2>&1 &");
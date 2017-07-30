<?php
$input = file_get_contents("php://input");
/*$input = '{"update_id":344186460,
"message":{"message_id":4597,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":-1001128531173,"title":"LTM Group","type":"supergroup"},"date":1501419029,"reply_to_message":{"message_id":4567,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":-1001128531173,"title":"LTM Group","type":"supergroup"},"date":1501416518,"forward_from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"forward_date":1501416452,"photo":[{"file_id":"AgADBQADw6cxG2798Fd_PWCyJCiqlxkWzDIABJeuTdc00MhIsPoAAgI","file_size":1054,"file_path":"photos/6336843325054756803.jpg","width":90,"height":90},{"file_id":"AgADBQADw6cxG2798Fd_PWCyJCiqlxkWzDIABPirMLSJ67tosfoAAgI","file_size":10392,"width":320,"height":320},{"file_id":"AgADBQADw6cxG2798Fd_PWCyJCiqlxkWzDIABBkenG_GkUb9svoAAgI","file_size":29781,"width":660,"height":660}]},"text":"/save #vim semacam teks editor","entities":[{"type":"bot_command","offset":0,"length":5},{"type":"hashtag","offset":6,"length":4}]}}

';*/

print shell_exec("nohup /usr/bin/php ".__DIR__."/run.php '".base64_encode($input)."' >> nh.out 2>&1 &");
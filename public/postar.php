<?php
$isi = '
{"update_id":344185986,
"message":{"message_id":7462,"from":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","language_code":"en-US"},"chat":{"id":243692601,"first_name":"Ammar","last_name":"F","username":"ammarfaizi2","type":"private"},"date":1501403968,


"text":"/warn"}}


';
$ch = curl_init("http://localhost:8000/telegram/webhook.php");
$op = [
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $isi,
];
curl_setopt_array($ch, $op);
echo curl_exec($ch);
curl_close($ch);
echo "\n\n";
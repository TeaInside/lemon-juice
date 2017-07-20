<?php
require __DIR__."/config.php";
$a = file_get_contents("a.tmp");
$pdo = new PDO(PDO_CONNECT, PDO_USER, PDO_PASS);
$st = $pdo->prepare("INSERT INTO `kb_kamus` (id, kata) VALUES (:id, :kata);");
$a = explode('<div class="row"><div class="col-md-2 col-sm-3 col-xs-4">', $a, 2);
$a = explode('</nav>', $a[1])[0];
$a = explode('<nav', $a)[0];
$a = explode("<li>", $a);
$ct = count($a);
for ($i=1; $i < $ct; $i++) { 
	$w = preg_match_all("#<a href=\"(.+)\">(.*)</a></li>#", $a[$i], $n);
	$st->execute([
			"id" => null,
			"kata" => strtolower(html_entity_decode($n[2][0], ENT_QUOTES, 'UTF-8'))
		]);
}

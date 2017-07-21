<?php
require __DIR__."/config.php";

$pg = 1;
do {
	$a = curl("http://www.kbbi.co.id/daftar-kata?page=".$pg++);
	$pdo = new PDO(PDO_CONNECT, PDO_USER, PDO_PASS);
	$st = $pdo->prepare("INSERT INTO `kb_kamus` (id, kata) VALUES (:id, :kata);");
	$a = explode('<div class="row"><div class="col-md-2 col-sm-3 col-xs-4">', $a, 2);
	if (count($a) != 0) {
		# code...
	
	$a = explode('</nav>', $a[1])[0];
	$a = explode('<nav', $a)[0];
	$a = explode("<li>", $a);
	$ct = count($a);
	for ($i=1; $i < $ct; $i++) { 
		$w = preg_match_all("#<a href=\"(.+)\">(.*)</a></li>#", $a[$i], $n);
		print $n[2][0]." | ".($st->execute([
				"id" => null,
				"kata" => strtolower(html_entity_decode($n[2][0], ENT_QUOTES, 'UTF-8'))
			])?"true":"false")."\n";
	}} else break;
} while (true);





function curl($url)
{
	$ch = curl_init($url);
	curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true
		]);
	$out = curl_exec($ch);
	curl_close($ch);
	return $out;
}

/**
 * https://www.joyofdata.de/blog/comparison-of-string-distance-algorithms/
 * http://www.geeksforgeeks.org/searching-for-patterns-set-2-kmp-algorithm/
 */

<?php
if (isset($_GET['q'])) {
	header("Content-type:application/json") xor
	print json_encode([
			"url 1"
		]) and die();
} elseif (isset($_GET['w'])) {
	header("Content-type:application/json") xor
	print json_encode([
			"url 2"
		]) and die();
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Test AJAX multi URL</title>
</head>
<body>
<div>
	<button id="ck">Click me !</button>
</div>
<div id="cd"></div>
<script type="text/javascript">
	document.getElementById("ck").addEventListener("click", function(){
		var z = function(){
			document.getElementById("cd").innerHTML+= this.responseText+" sukses<br>";
		}, a = new XMLHttpRequest(), b = new XMLHttpRequest();
        a.open("GET","?q",true);
        a.onload = z;
        a.send(null);
        b.open("GET","?w",true);
        b.onload = z;
        b.send(null);
	});
</script>
</body>
</html>
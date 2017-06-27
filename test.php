<?php
require __DIR__."/vendor/autoload.php";

use App\MyAnimeList\MyAnimeList;

define("data", __DIR__."/data");

$a = new MyAnimeList("ammarfaizi2", "triosemut123");
// $a->search("naruto");
// $a->exec();
// $aa = $a->get_info(20);
$aa = $a->simple_search("naruto ");

var_dump($aa);

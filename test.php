<?php
require __DIR__."/vendor/autoload.php";

use App\MyAnimeList\MyAnimeList;
use App\WhatAnime\WhatAnime;

#define("data", __DIR__."/data");

/*$a = new MyAnimeList("ammarfaizi2", "triosemut123");
// $a->search("naruto");
// $a->exec();
// $aa = $a->get_info(20);
$aa = $a->simple_search("naruto ");

var_dump($aa);
*/

$a = new WhatAnime("https://www.funimationfilms.com/wp-content/uploads/2016/01/psycho-pass-trailer02_small.jpg");
$a = json_decode($a->exec(), 1);
$a = $a['docs'][0];
$url = "https://whatanime.ga/".$a['season']."/".$a['anime']."/".$a['file']."?start=".$a['start']."&end=".$a['end']."&token=".$a['token'];
#var_dump($a, $url, date("Y-m-d H:i:s", $a['expires']));
$a = new IceTeaSystem\Curl($url);
$a->set_opt(array(
				CURLOPT_REFERER	=> "https://whatanime.ga/",
				CURLOPT_HTTPHEADER => array(
            		"X-Requested-With: XMLHttpRequest",
            		"Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
            	)
			)
		);
header("Content-Type: video/mp4");
print $a->exec();
/*

            "from": 381.417,
            "to": 382,
            "i": 0,
            "start": 365.417,
            "end": 386,
            "t": 382,
            "season": "2009-10",
            "anime": "\u6230\u9b25\u53f8\u66f8",
            "file": "[DmzJ][Tatakau_Shishou_the_Book_of_Bantorra][BDRIP][06][1280X720].mp4",
            "episode": 6,
            "expires": 1498589434,
            "token": "gr3wihdhgCd8N00xvyClZw",
            "tokenthumb": "k6PP61vvxJiG0xJcNWysBQ",
            "diff": 9.391262,
            "title": "\u6226\u3046\u53f8\u66f8 The Book of Bantorra",
            "title_english": "Armed Librarians: The Book of Bantorra",
            "title_romaji": "Tatakau Shisho: The Book of Bantorra"



https://whatanime.ga/2009-10/%E6%88%B0%E9%AC%A5%E5%8F%B8%E6%9B%B8/[DmzJ][Tatakau_Shishou_the_Book_of_Bantorra][BDRIP][06][1280X720].mp4?start=365.417&end=386&token=gr3wihdhgCd8N00xvyClZw
https://whatanime.ga/2009-10/%E6%88%B0%E9%AC%A5%E5%8F%B8%E6%9B%B8/[DmzJ][Tatakau_Shishou_the_Book_of_Bantorra][BDRIP][06][1280X720].mp4?start=365.417&end=386&token=gr3wihdhgCd8N00xvyClZw










[JyFanSub][91Days][08][GB][720p].mp4*/
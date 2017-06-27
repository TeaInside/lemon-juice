<?php

namespace App\WhatAnime;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\WhatAnime
 * @since 0.0.1
 */

use IceTeaSystem\Curl;
use App\WhatAnime\WhatAnimeContract;
use App\WhatAnime\WhatAnimeException;

defined("data") or die("Data not defined !\n");

class WhatAnime implements WhatAnimeContract
{
	/**
	 * @var string
	 */
	private $image;

	/**
	 * Constructor.
	 *
	 * @param string
	 */
	public function __construct($image)
	{
		if (filter_var($image, FILTER_VALIDATE_URL)) {
			$ch = new Curl($image);
			$this->image = $ch->exec();
			#header("Content-Type:image/jpg");
			#var_dump(base64_encode($this->image));die;
		} else {
			$this->image = $image;
		}
	}
	
	/**
	 * Execute search.
	 */
	public function exec()
	{
		$ch = new Curl("https://whatanime.ga/search");
		$ch->post("data=data%3Aimage%2Fjpeg%3Bbase64%2C".urlencode(base64_encode($this->image)));
		$ch->set_opt(array(
				CURLOPT_REFERER	=> "https://whatanime.ga/",
				CURLOPT_HTTPHEADER => array(
            		"X-Requested-With: XMLHttpRequest",
            		"Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
            	)
			)
		);
		return $ch->exec();
	}
}
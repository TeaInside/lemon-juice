<?php

namespace App\WhatAnime;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\WhatAnime
 * @since 0.0.1
 */

use Sys\Curl;
use App\WhatAnime\WhatAnimeContract;
use App\WhatAnime\WhatAnimeException;

defined("data") or die("Data not defined !\n");

class WhatAnime implements WhatAnimeContract
{
    /**
     * @var string
     */
    private $image;

    public function __construct($image_binary)
    {
        $this->image = base64_encode($image_binary);
    }

    public function exec()
    {
        $ch = new Curl("https://whatanime.ga/search");
        $ch->post("data=data%3Aimage%2Fjpeg%3Bbase64%2C".urlencode($this->image));
        $ch->set_opt(
            [
                CURLOPT_REFERER    => "https://whatanime.ga/",
                CURLOPT_HTTPHEADER => [
                    "X-Requested-With: XMLHttpRequest",
                    "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
                ]
            ]
        );
        return json_encode(json_decode($ch->exec(), true), 128);
    }
}

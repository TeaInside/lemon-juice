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
defined("PUBLIC_DIR") or die("PUBLIC_DIR not defined!\n");

class WhatAnime implements WhatAnimeContract
{
    /**
     * @var string
     */
    private $image;

    /**
     * @var string
     */ 
    private $hash;

    /**
     * @var string
     */
    private $new_data;

    public function __construct($image_binary)
    {
        $this->image = base64_encode($image_binary);
        $this->hash = sha1($image_binary);
    }

    public function exec()
    {
        if ($a = $this->check_old()) {
            return $a;
        } else {
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
            $this->new_data = json_encode(json_decode($ch->exec(), true), 128);
            $this->hash_manage();
            return $this->new_data;
        }
    }

    private function check_old()
    {
        if (file_exists(PUBLIC_DIR."/whatanime/hash/".$this->hash.".json")) {
            $st = json_decode($rr = file_get_contents(PUBLIC_DIR."/whatanime/hash/".$this->hash.".json"), true);
            if ($st) {
                return $st;
            }
        }
        return false;
    }

    private function hash_manage()
    {
        is_dir($a = PUBLIC_DIR."/whatanime/video") or shell_exec("mkdir -p ".$a);
        is_dir($a = PUBLIC_DIR."/whatanime/hash") or shell_exec("mkdir -p ".$a);
        file_put_contents(PUBLIC_DIR."/whatanime/hash/".$this->hash.".json", $this->new_data);
    }
}

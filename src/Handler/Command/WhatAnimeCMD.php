<?php

namespace Handler\Command;

use Curl;

class WhatAnimeCMD
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

    public static function check_video($url)
    {
        return file_exists(PUBLIC_DIR."/whatanime/video/".(sha1($url)).".mp4");
    }

    public static function download_video($url)
    {
        $ch = new Curl($url);
        $ch->set_opt(
            [
                CURLOPT_REFERER    => "https://whatanime.ga/",
                CURLOPT_HTTPHEADER => [
                    "X-Requested-With: XMLHttpRequest",
                    "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"
                ]
            ]
        );
        is_dir(PUBLIC_DIR."/whatanime/video/") or shell_exec("mkdir -p ".PUBLIC_DIR."/whatanime/video/");
        $handle = fopen(PUBLIC_DIR."/whatanime/video/".($vidname = sha1($url)).".mp4", "w");
        fwrite($handle, $ch->exec());
        fclose($handle);
        return $vidname;
    }
}


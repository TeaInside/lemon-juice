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

    public static function make_cache($hash, $data)
    {
        $a = json_decode(file_get_contents(PUBLIC_DIR."/whatanime/cache_control.json"), true);
        $a[] = $hash;
        file_put_contents(PUBLIC_DIR."/whatanime/cache_control.json", json_encode($a, 128));
        return file_put_contents(PUBLIC_DIR."/whatanime/cache/".$hash.".json", json_encode($data, 128));
    }

    public static function check_cache($file_id)
    {
        $a = json_decode(file_get_contents(PUBLIC_DIR."/whatanime/cache_control.json"), true);
        if (in_array($hash = sha1($file_id), $a)) {
            return json_decode(file_get_contents(PUBLIC_DIR."/whatanime/cache/".$hash.".json"), true);
        } else {
            return false;
        }
    }

    public static function cache_control($cache)
    {
        is_dir(PUBLIC_DIR."/whatanime/cache") or shell_exec("mkdir -p ".PUBLIC_DIR."/whatanime/cache");
        is_dir(PUBLIC_DIR."/whatanime") or shell_exec("mkdir -p ".PUBLIC_DIR."/whatanime");
        file_exists(PUBLIC_DIR."/whatanime/cache_control.json") or file_put_contents(PUBLIC_DIR."/whatanime/cache_control.json", json_encode([], 128));
    }

    public static function check_video($url)
    {
        return file_exists(PUBLIC_DIR."/whatanime/video/".($vidname = sha1($url)).".mp4") ? $vidname : false;
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


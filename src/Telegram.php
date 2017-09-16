<?php

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
final class Telegram
{
    /**
     * @param string $method
     * @param array  $param
     */
    public static function __callStatic($method, $param)
    {
        if (isset($param[1]) and $param[1] == "GET") {
            $ch = curl_init("https://api.telegram.org/bot".TOKEN."/".$method.(isset($param[0]) ? "?".http_build_query($param[0]) : ""));
            $op = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
            ];
        } else {
            $ch = curl_init("https://api.telegram.org/bot".TOKEN."/".$method);
            $op = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POSTFIELDS => http_build_query($param[0]),
                CURLOPT_POST => true,
            ];
        }
        if (isset($param[2])) {
            foreach ($param[2] as $key => $val) {
                $op[$key] = $val;
            }
        }
        curl_setopt_array($ch, $op);
        print $out = curl_exec($ch);
        $err = curl_error($ch) and $out = $err;
        $info = curl_getinfo($ch);
        curl_close($ch);
        return [
            "content" => $out,
            "info" => $info
        ];
    }
}

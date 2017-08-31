<?php

namespace App\PHPVirtual;

defined("PHPVIRTUAL_DIR") or die("PHPVIRTUAL_DIR not defined!");
defined("PHPVIRTUAL_URL") or die("PHPVIRTUAL_URL not defined!");

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
final class PHPVirtual
{
    public static function run($code, $sudo = false)
    {
        is_dir(PHPVIRTUAL_DIR) or shell_exec("mkdir -p ".PHPVIRTUAL_DIR);
        $file = sha1($code).".php";
        if (file_exists(PHPVIRTUAL_DIR."/".$file)) {
            return self::__exec(PHPVIRTUAL_URL."/".$file, $file);
        } else {
            $handle = fopen(PHPVIRTUAL_DIR."/".$file, "w");
            fwrite($handle, $code);
            fclose($handle);
            return self::__exec(PHPVIRTUAL_URL."/".$file, $file);
        }
    }

    private static function __exec($url, $file)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:46.0) Gecko/20100101 Firefox/46.0",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer NTIwOGRjZDJlOGYwMjNkMGZlODFjMmZlYTVhNDY5ZjhmYTg1Y2I3OA=="
                ],
                CURLOPT_TIMEOUT => 5,
                CURLOPT_CONNECTTIMEOUT => 5
            ]);
        $out = curl_exec($ch);
        $err = curl_error($ch) and $out = $err;
        curl_close($ch);
        return str_replace(PHPVIRTUAL_DIR."/".$file, "/tmp/phpvirtual/".substr($file, 0, 5).".php", $out);
    }
}

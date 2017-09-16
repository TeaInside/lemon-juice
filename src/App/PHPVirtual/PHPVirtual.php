<?php

namespace App\PHPVirtual;

<<<<<<< HEAD
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
        curl_setopt_array(
            $ch,
            [
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
            ]
        );
        $out = curl_exec($ch);
        $err = curl_error($ch) and $out = $err.".";
        curl_close($ch);
        return str_replace(PHPVIRTUAL_DIR."/".$file, "/tmp/phpvirtual/".substr($file, 0, 5).".php", $out);
=======
/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\PHPVirtual
 * @since 0.0.1
 */

use IceTeaSystem\Curl;
use App\PHPVirtual\PHPVirtualContract;
use App\PHPVirtual\PHPVirtualException;

defined("PHPVIRTUAL_DIR") or die("PHPVIRTUAL_DIR not defined!\n");
defined("PHPVIRTUAL_URL") or die("PHPVIRTUAL_URL not defined!\n");

class PHPVirtual implements PHPVirtualContract
{
    /**
     * @var string
     */
    private $php_code;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $output;

    /**
     * Constructor.
     *
     * @param string $php_code
     */
    public function __construct($php_code)
    {
        is_dir(PHPVIRTUAL_DIR) or mkdir(PHPVIRTUAL_DIR);
        is_dir(PHPVIRTUAL_DIR) or shell_exec("mkdir -p ".PHPVIRTUAL_DIR);
        $this->php_code        = $php_code;
        $this->hash            = sha1($php_code);
        $this->dir_replace    = array(
                array(
                    PHPVIRTUAL_DIR."/".$this->hash.".php",
                    realpath(PHPVIRTUAL_DIR)."/".$this->hash.".php",
                    realpath(PHPVIRTUAL_DIR)."\\".$this->hash.".php"
                ),
                array(
                    "/tmp/vphp/".substr($this->hash, 0, 5).".php",
                    "/tmp/vphp/".substr($this->hash, 0, 5).".php",
                    "/tmp/vphp/".substr($this->hash, 0, 5).".php"
                )
            );
    }

    /**
     * Create php file.
     *
     * @return int|false
     */
    private function create_file()
    {
        $handle = fopen(PHPVIRTUAL_DIR."/".$this->hash.".php", "w");
        $write  = fwrite($handle, $this->php_code);
        fclose($handle);
        return $write;
    }

    /**
     * Execute php virtual.
     *
     * @return string
     */
    public function execute()
    {
        if (!file_exists(PHPVIRTUAL_DIR."/".$this->hash.".php")) {
            $this->create_file();
        }
        $ch = new Curl(PHPVIRTUAL_URL."/".$this->hash.".php");
        $ch->set_opt(
            array(
                CURLOPT_TIMEOUT             => 5,
                CURLOPT_CONNECTTIMEOUT     => 5
            )
        );
        $out = $ch->exec();
        if (count($this->dir_replace)) {
            $out = str_replace($this->dir_replace[0], $this->dir_replace[1], $out);
        }
        return $out;
>>>>>>> 839408767998202ded85e46370449b4b86967412
    }
}

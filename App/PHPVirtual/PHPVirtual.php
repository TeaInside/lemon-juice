<?php

namespace App\PHPVirtual;

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
            $out = str_replace($this->out_replace[0], $this->out_replace[1], $out);
        }
        return $out;
    }
}

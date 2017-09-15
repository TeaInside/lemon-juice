<?php

namespace App\VGMdb;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\VGMdb
 * @since 0.0.1
 */

class VGMdbException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
    }
}

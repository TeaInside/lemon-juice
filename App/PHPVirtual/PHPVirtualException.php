<?php

namespace App\PHPVirtual;

use Exception;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\PHPVirtual
 * @since 0.0.1
 */

class PHPVirtualException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
    }
}

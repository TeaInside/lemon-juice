<?php

namespace App\PHPVirtual;

use Exception;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\JavaVirtual
 * @since 0.0.1
 */

class JavaVirtualException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
    }
}

<?php

namespace App\WhatAnime;

use Exception;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\WhatAnime
 * @since 0.0.1
 */

class WhatAnimeException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
    }
}

<?php

namespace App\MyAnimeList;

use Exception;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\MyAnimeList
 * @since 0.0.1
 */

class MyAnimeListException extends Exception
{
    public function __construct($msg, $code)
    {
        parent::__construct($msg, $code);
    }
}

<?php

namespace Sys\Hub;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 *
 * Singleton pattern.
 */

trait Singleton
{
    protected static $instance;
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    protected function __clone()
    {
    }
    
    protected function __wakeup()
    {
    }
    
    protected function __sleep()
    {
    }

    protected function __construct()
    {
    }
}

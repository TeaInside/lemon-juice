<?php

use Telegram as B;
use Handler\MainHandler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @license MIT
 */
final class Bot
{
    private $in;

    public function __construct($in = null)
    {
        $this->in = $in ? json_decode($in, true, 512, JSON_BIGINT_AS_STRING) : json_decode(file_get_contents("php://input"), true, 512, JSON_BIGINT_AS_STRING);
    }

    public function run()
    {
        $handler = new MainHandler($this->in);
        $handler->parseEvent();
        $handler->runHandler();
        $handler->__save_event();
    }
}

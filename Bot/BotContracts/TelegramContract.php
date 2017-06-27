<?php

namespace Bot\BotContracts;

interface TelegramContract
{
    /**
     * Constructor.
     * 
     * @param string $token
     */
    public function __construct($token);

    /**
     * Run.
     * 
     * @param string $token
     */
    public static function run($token);
}

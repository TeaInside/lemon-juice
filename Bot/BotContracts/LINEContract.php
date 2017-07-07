<?php

namespace Bot\BotContracts;

interface LINEContract
{
    /**
     * Constructor.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public function __construct($channel_token, $channel_secret);

    /**
     * Run.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public static function run($channel_token, $channel_secret);
}

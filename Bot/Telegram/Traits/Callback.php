<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait Callback
{
    /**
     * Save callback flag
     */
    private function save_callback_flag()
    {
        file_put_contents(storage."/telegram/callback_flag.txt", json_encode($this->callback_flag_data, 128));
    }

    /**
     * Load callback flag data
     */
    private function load_callback_flag_data()
    {
        if (file_exists(storage."/telegram/callback_flag.txt")) {
            $this->callback_flag_data = json_decode(file_get_contents(storage."/telegram/callback_flag.txt"), true);
            $this->callback_flag_data = is_array($this->callback_flag_data) ? $this->callback_flag_data : array();
        } else {
            $this->callback_flag_data = array();
        }
    }

    /**
     * Parse callback
     */
    private function parseCallback()
    {
        
    }
}
<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait UserWarning
{
    /**
     * Count user warning
     */
    private function count_user_warning($uifo)
    {
        if (file_exists(storage."/telegram/user_warning_data.txt")) {
            $this->user_warning_data = json_decode(file_get_contents(storage."/telegram/user_warning_data.txt"), true);
        } else {
            $this->user_warning_data = array();
        }
        return isset($this->user_warning_data[$uifo]) ? $this->user_warning_data[$uifo] : 0;
    }

    /**
     * Save user warning
     */
    private function save_warning_data()
    {
        file_put_contents(storage."/telegram/user_warning_data.txt", json_encode($this->user_warning_data, 128));
    }
}
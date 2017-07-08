<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait WhatAnime
{
    /**
     * Save whatanime hash table
     */
    private function save_whatanime_hash()
    {
        file_put_contents("whatanime_hash_table.json", json_encode($this->whatanime_hash_table, 128));
    }

    /**
     * Load whatanime hash table
     */
    private function load_whatanime_data()
    {
        if (file_exists("whatanime_hash_table.json")) {
            $this->whatanime_hash_table = json_decode(file_get_contents("whatanime_hash_table.json"), true);
            $this->whatanime_hash_table = is_array($this->whatanime_hash_table) ? $this->whatanime_hash_table : array();
        } else {
            $this->whatanime_hash_table = array();
        }
    }
}

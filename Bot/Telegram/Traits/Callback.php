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
        $a = json_decode($this->callback_data, true);
        #var_dump($a);
        $callback_cmd = array(
                "rw",
                "cw"
            );
        switch ($a['cmd']) {
            case 'rw':
                    $this->cancel_warning($a['c']);
                break;
            case 'cw':

                break;
            default:
                # code...
                break;
        }
    }

    private function cancel_warning($uifo)
    {
        $a = json_decode(file_get_contents(storage."/telegram/user_warning_data.txt"), true);
        if (isset($a[$uifo])) {
            $a[$uifo] = $a[$uifo]-1;
            file_put_contents(storage."/telegram/user_warning_data.txt", json_encode($a, 128));
            $msg = "Berhasil membatalkan peringatan.\n\nJumlah peringatan {user} sekarang <b>".($a[$uifo])."</b>";
        } else {
            $msg = "Action cancel_warning failed !";
        }
        $this->textReply($msg, null, null, array("parse_mode"=>"HTML"));
    }
}
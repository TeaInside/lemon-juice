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
        $this->load_callback_flag_data();
        $a = json_decode($this->callback_data, true);
        if (!$this->callback_flag_data[$a['f']]) {
            $callback_cmd = array(
                "rw",
                "cw"
            );
            $this->callback_flag_data[$a['f']] = true;
            $text = $this->event['callback_query']['message']['text'];
            switch ($a['cmd']) {
            case 'rw':
                    $user = explode(" ", $text, 2);
                    $user = $user[0];
                    $this->remove_warning($a['c'], $user);
                    $this->tel->editMessageText($this->event['message']['chat']['id'], $this->event['message']['id'], $this->event['message']['text'], array("parse_mode"=>"HTML","reply_markup"=>null));
                break;
            case 'cw':
                    $user = explode(" ", $text, 2);
                    $user = $user[0];
                    $this->cancel_warning($a['c'], $user);
                break;
            default:
                    
                break;
            }
        }
    }

    private function cancel_warning($uifo, $user = null)
    {
        if ($this->check_admin($this->event['callback_query']['from']['id'])) {
            $a = json_decode(file_get_contents(storage."/telegram/user_warning_data.txt"), true);
            if (isset($a[$uifo])) {
                $a[$uifo] = $a[$uifo]-1;
                file_put_contents(storage."/telegram/user_warning_data.txt", json_encode($a, 128));
                if ($a[$uifo]==0) {
                    $msg = "{$user} bebas dari peringatan.";
                } else {
                    $msg = "Berhasil membatalkan peringatan.\n\nJumlah peringatan {$user} sekarang <b>".($a[$uifo])."</b>";
                }
            } else {
                $msg = "Action cancel_warning failed !";
            }
            $this->save_callback_flag();
            $this->textReply($msg, null, null, array("parse_mode"=>"HTML"));
        }
    }


    private function remove_warning($uifo, $user = null)
    {
        if ($this->check_admin($this->event['callback_query']['from']['id'])) {
            $a = json_decode(file_get_contents(storage."/telegram/user_warning_data.txt"), true);
            if (isset($a[$uifo]) && $a[$uifo]>0) {
                $a[$uifo] = 0;
                file_put_contents(storage."/telegram/user_warning_data.txt", json_encode($a, 128));
                $msg = "Berhasil mereset peringatan.\n\n{$user} bebas dari peringatan.";
            } else {
                $msg = "Action remove_warning failed !";
            }
            $this->save_callback_flag();
            $this->textReply($msg, null, null, array("parse_mode"=>"HTML"));
        }
    }


    private function check_admin($userid)
    {
        return strpos($this->tel->getChatAdministrators($this->room), (string)$userid)!==false;
    }
}

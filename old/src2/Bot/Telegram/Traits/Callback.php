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
<<<<<<< HEAD
                case 'rw':
=======
            case 'rw':
>>>>>>> 839408767998202ded85e46370449b4b86967412
                    $text   = $this->event['callback_query']['message']['text'];
                    $user   = $user[0];
                    $this->remove_warning($a['c'], $user);
                    $aax = $this->tel->editMessageText($this->event['callback_query']['message']['chat']['id'], $this->event['callback_query']['message']['message_id'], $text, array("parse_mode"=>"HTML","reply_markup"=>null));
<<<<<<< HEAD
                    break;
                case 'cw':
=======
                break;
            case 'cw':
>>>>>>> 839408767998202ded85e46370449b4b86967412
                    $text   = $this->event['callback_query']['message']['text'];
                    $text   = explode("\n", $text);
                    $text   = trim($text[0])."\n\n".str_replace(array(0,1,2,3,4,5,6,7,8,9), array('<b>0</b>','<b>1</b>','<b>2</b>','<b>3</b>','<b>4</b>','<b>5</b>','<b>6</b>','<b>7</b>','<b>8</b>','<b>9</b>'), end($text));
                    $user = explode(" ", $text, 2);
                    $user = $user[0];
                    $this->cancel_warning($a['c'], $user);
                    $aax = $this->tel->editMessageText($this->event['callback_query']['message']['chat']['id'], $this->event['callback_query']['message']['message_id'], $this->event['callback_query']['message']['text'], array("parse_mode"=>"HTML","reply_markup"=>null));
<<<<<<< HEAD
                    break;
                default:
                    break;
=======
                break;
            default:
                    
                break;
>>>>>>> 839408767998202ded85e46370449b4b86967412
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
                $admin = $this->event['callback_query']['from']['username'];
                $admin = $admin ? "@".$admin : $this->event['callback_query']['from']['first_name'];
                if ($a[$uifo]==0) {
                    $msg = "{$user} bebas dari peringatan.";
                } else {
                    $msg = "Berhasil membatalkan peringatan oleh admin {$admin}.\nJumlah peringatan {$user} sekarang <b>".($a[$uifo])."</b>";
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
                $admin = $this->event['callback_query']['from']['username'];
                $admin = $admin ? "@".$admin : $this->event['callback_query']['from']['first_name'];
                $msg = "Berhasil mereset peringatan oleh admin ".($admin).".\n{$user} bebas dari peringatan.";
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

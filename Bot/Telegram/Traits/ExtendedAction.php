<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

use App\PHPVirtual\PHPVirtual;
use App\JavaVirtual\JavaVirtual;

trait ExtendedAction
{
    /**
     * Parse Extended.
     */
    private function parseExtendedAction()
    {
        $text = $this->event['message']['text'];
        if (substr($text, 0, 5) == "<?php") {
            $a = new PHPVirtual($text);
            $out = $a->execute();
            if (empty($out)) {
                $out = "~";
            } else {
                $out = str_replace("<br />", "\n", $out);
            }
            $this->textReply($out, null, $this->event['message']['message_id'], array(
                    "parse_mode" => "HTML"
                ));
        } elseif (substr($text, 0, 6) == "<?java") {
            $a = new JavaVirtual($text);
            $out = $a->execute();
            if (empty($out)) {
                $out = "~";
            }
            $this->textReply($out, null, $this->event['message']['message_id']);
        }
    }
}

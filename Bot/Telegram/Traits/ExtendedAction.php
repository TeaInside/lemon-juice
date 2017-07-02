<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

use App\PHPVirtual\PHPVirtual;

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
        } elseif (substr($text, 0, 5) == "<?php") {
            // code...
        }
    }
}

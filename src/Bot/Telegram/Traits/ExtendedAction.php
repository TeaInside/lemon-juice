<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */
use AI\AI;
use App\CVirtual\CVirtual;
use App\PHPVirtual\PHPVirtual;
use App\JavaVirtual\JavaVirtual;
use App\RubyVirtual\RubyVirtual;

trait ExtendedAction
{
    /**
     * Parse Extended.
     */
    private function parseExtendedAction()
    {
        $text = $this->event['message']['text'];
        if (strtolower(substr($text, 0, 5)) == "<?php") {
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
        } elseif ($tx = strtolower(substr($text, 0, 6)) and $tx == "<?java") {
            $a = new JavaVirtual(substr($text, 6));
            $out = $a->execute();
            if (empty($out)) {
                $out = "~";
            }
            $this->textReply($out, null, $this->event['message']['message_id']);
        } elseif ($tx == "<?ruby") {
            $a = new RubyVirtual(substr($text, 6));
            $out = $a->execute();
            if (empty($out)) {
                $out = "~";
            }
            $this->textReply($out, null, $this->event['message']['message_id']);
        } elseif (strtolower(substr($text, 0, 3)) == "<?c") {
            $a = new CVirtual(substr($text, 3));
            $out = $a->execute();
            if (empty($out)) {
                $out = "~";
            }
            $this->textReply($out, null, $this->event['message']['message_id']);
        } elseif ($tx == "shexec") {
            if ($this->safety_shell_exec($sh = substr($text, 6))) {
                $a = shell_exec($sh. " 2>&1");
                $a = empty($a) ? "~" : $a;
            } else {
                $a = "Rejected for security reason!";
            }
            $this->textReply($a, null, $this->event['message']['message_id']);
        } else {
            $ai = new AI();
            $ai->input($text, $this->actor);
            if ($ai->execute()) {
                $out = $ai->output();
                if (isset($out['text'][0])) {
                    $this->textReply($out['text'][0], null, $this->event['message']['message_id']);
                }
            }
        }
    }

    /**
     * @param string
     */
    private function safety_shell_exec($str)
    {
        $str = strtolower($str);
        if (
            // super userid
            $this->actor_id != 24369260 and (
                strpos($str, "sudo ")!==false or
                strpos($str, "rm ")!==false or
                strpos($str, "apt ")!==false or
                strpos($str, "pass")!==false
            )
        ) {
            return false;
        } else {
            return true;
        }
    }
}


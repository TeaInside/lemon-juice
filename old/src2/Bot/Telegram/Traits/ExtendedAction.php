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
            if ($this->safety($text, "php")) {
                $a = new PHPVirtual($text);
                $out = $a->execute();
                if (empty($out)) {
                    $out = "~";
                } else {
                    $out = str_replace("<br />", "\n", $out);
                }
            } else {
                $out = "Rejected for security reason!";
            }
            $this->textReply(
<<<<<<< HEAD
                $out,
                null,
                $this->event['message']['message_id'],
                array(
=======
                $out, null, $this->event['message']['message_id'], array(
>>>>>>> 839408767998202ded85e46370449b4b86967412
                    "parse_mode" => "HTML"
                )
            );
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
            if ($this->safety($sh = substr($text, 6), "sh")) {
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
     * @param string
     */
    private function safety($str, $type)
    {
        $str = strtolower($str);
        $rt = true;
        switch ($type) {
<<<<<<< HEAD
            case 'sh':
                if ($this->actor_id != 243692601 and (strpos($str, "sudo ")!==false
                or strpos($str, "rm ")!==false
                or strpos($str, "apt ")!==false
                or strpos($str, "pass")!==false)
                ) {
                    $rt = false;
                }
                break;
            case 'php':
                if ($this->actor_id != 243692601 and (strpos($str, "shell_exec")!==false
=======
        case 'sh':
            if ($this->actor_id != 243692601 and (strpos($str, "sudo ")!==false
                or strpos($str, "rm ")!==false
                or strpos($str, "apt ")!==false
                or strpos($str, "pass")!==false)
            ) {
                $rt = false;
            }
            break;
        case 'php':
            if ($this->actor_id != 243692601 and (strpos($str, "shell_exec")!==false
>>>>>>> 839408767998202ded85e46370449b4b86967412
                or strpos($str, "exec")!==false
                or strpos($str, "system")!==false
                or strpos($str, "unlink")!==false
                or strpos($str, "scandir")!==false
                or strpos($str, "eval") !== false)
<<<<<<< HEAD
                ) {
                    $rt = false;
                }
                break;
            default:
                break;
=======
            ) {
                $rt = false;
            }
            break;
        default:
                    
            break;
>>>>>>> 839408767998202ded85e46370449b4b86967412
        }
        return $rt;
    }
}

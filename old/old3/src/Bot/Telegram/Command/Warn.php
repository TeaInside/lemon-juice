<?php

namespace Bot\Telegram\Command;

use PDO;
use Sys\DB;
use Bot\Telegram\B;

defined("DBHOST") or require __DIR__."/../../../config/telegram.php";

class Warn
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor.
     * @param array @warn_data
     */
    public function __construct($warn_data)
    {
        $this->data = $warn_data;
    }

    public function run()
    {
        if (isset($this->data['auto']) && $this->data['auto'] === true) {
            $this->auto();
        } else {
            $this->manual();
        }
    }

    private function auto()
    {
        $pdo = DB::pdoInstance();
        $st = $pdo->prepare("SELECT `warn_count`,`reason` FROM `gm_user_warning` WHERE `uifd`=:uifd LIMIT 1;");
        $st->execute([':uifd'=>$this->data['uifd']]);
        $user = "<b>Auto warn :</b>\n".(isset($this->data['username']) ? "<a href=\"https://telegram.me/".$this->data['username']."\">".$this->data['actor']."</a>" : $this->data['actor']);
        if ($st = $st->fetch(PDO::FETCH_NUM) or $st[0] === 0) {
            if ($st[0] >= 2) {
                $res = json_decode($st[1], true);
                $res[$this->data['warner']][] = $this->data['reason'];
                $st = $pdo->prepare("UPDATE `gm_user_warning` SET `warn_count`=`warn_count`+1, `reason`=:reason, `updated_at`=:updated_at WHERE `uifd`=:uifd LIMIT 1;");
                $st->execute(
                    [
                        ":reason" => json_encode($res),
                        ":uifd" => $this->data['uifd'],
                        ":updated_at" => date("Y-m-d H:i:s")
                    ]
                );
                $a = B::restrictChatMember(
                    [
                        "chat_id" => $this->data['room_id'],
                        "user_id" => $this->data['userid']
                    ]
                );
                $b = B::kickChatMember($this->data['room_id'], $this->data['userid']);
                if ($a == '{"ok":true,"result":true}' or $b == '{"ok":true,"result":true}') {
                    B::sendMessage("{$user} <b>banned</b> : reached the max number of warnings (<code>3/3</code>)\n\n<b>Reason :</b>\nBad word.", $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true]);
                } else {
                    B::sendMessage("{$user} <b>banned</b> : reached the max number of warnings (<code>3/3</code>)\n\n<b>Reason :</b>\nBad word.\n\n".$a."\n\n".$b, $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true]);
                }
            } else {
                $res = json_decode($st[1], true);
                $res[$this->data['warner']][] = $this->data['reason'];
                $st = $pdo->prepare("UPDATE `gm_user_warning` SET `warn_count`=`warn_count`+1, `reason`=:reason, `updated_at`=:updated_at WHERE `uifd`=:uifd LIMIT 1;");
                $st->execute(
                    [
                        ":reason" => json_encode($res),
                        ":uifd" => $this->data['uifd'],
                        ":updated_at" => date("Y-m-d H:i:s")
                    ]
                );
                print B::sendMessage("{$user} <b>has been warned</b> (<code>2/3</code>)\n\n<b>Reason :</b>\nBad word.", $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true, "reply_markup"=>json_encode(["inline_keyboard"=>[[["text"=>"Remove warn","callback_data"=> "/nowarn ".$this->data['uifd']]]]])]);
            }
        } else {
            $st = $pdo->prepare("INSERT INTO `gm_user_warning` (`uifd`, `userid`, `reason`, `warn_count`, `room_id`, `created_at`) VALUES (:uifd, :userid, :reason, :warn_count, :room_id, :created_at);");
            $st->execute(
                [
                    ":uifd" => $this->data['uifd'],
                    ":userid" => $this->data['userid'],
                    ":reason" => json_encode([$this->data['warner'] => [$this->data['reason']]]),
                    ":warn_count" => 1,
                    ":room_id" => $this->data['room_id'],
                    ":created_at" => date("Y-m-d H:i:s")
                ]
            );
            print B::sendMessage("{$user} <b>has been warned</b> (<code>1/3</code>)\n\n<b>Reason :</b>\nBad word.", $this->data['room_id'], $this->data['reply_to'], ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true, "reply_markup"=>json_encode(["inline_keyboard"=>[[["text"=>"Remove warn","callback_data"=> "/nowarn ".$this->data['uifd']]]]])]);
        }
    }


    private function manual()
    {
        $pdo = DB::pdoInstance();
        $st = $pdo->prepare("SELECT `warn_count`,`reason` FROM `gm_user_warning` WHERE `uifd`=:uifd LIMIT 1;");
        $st->execute([':uifd'=>$this->data['uifd']]);
        $user = isset($this->data['username']) ? "<a href=\"https://telegram.me/".$this->data['username']."\">".$this->data['actor']."</a>" : $this->data['actor'];
        if ($st = $st->fetch(PDO::FETCH_NUM) or $st[0] === 0) {
            if ($st[0] >= 2) {
                $res = json_decode($st[1], true);
                $res[$this->data['warner']][] = $this->data['reason'];
                $st = $pdo->prepare("UPDATE `gm_user_warning` SET `warn_count`=`warn_count`+1, `reason`=:reason, `updated_at`=:updated_at WHERE `uifd`=:uifd LIMIT 1;");
                $st->execute(
                    [
                        ":reason" => json_encode($res),
                        ":uifd" => $this->data['uifd'],
                        ":updated_at" => date("Y-m-d H:i:s")
                    ]
                );
                $a = B::restrictChatMember(
                    [
                        "chat_id" => $this->data['room_id'],
                        "user_id" => $this->data['userid']
                    ]
                );
                $b = B::kickChatMember($this->data['room_id'], $this->data['userid']);
                if ($a == '{"ok":true,"result":true}' or $b == '{"ok":true,"result":true}') {
                    B::sendMessage("{$user} <b>banned</b> : reached the max number of warnings (<code>3/3</code>)", $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true]);
                } else {
                    B::sendMessage("{$user} <b>banned</b> : reached the max number of warnings (<code>3/3</code>)\n\n".$a."\n\n".$b, $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true]);
                }
            } else {
                $res = json_decode($st[1], true);
                $res[$this->data['warner']][] = $this->data['reason'];
                $st = $pdo->prepare("UPDATE `gm_user_warning` SET `warn_count`=`warn_count`+1, `reason`=:reason, `updated_at`=:updated_at WHERE `uifd`=:uifd LIMIT 1;");
                $st->execute(
                    [
                        ":reason" => json_encode($res),
                        ":uifd" => $this->data['uifd'],
                        ":updated_at" => date("Y-m-d H:i:s")
                    ]
                );
                print B::sendMessage("{$user} <b>has been warned</b> (<code>2/3</code>)", $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true, "reply_markup"=>json_encode(["inline_keyboard"=>[[["text"=>"Remove warn","callback_data"=> "/nowarn ".$this->data['uifd']]]]])]);
            }
        } else {
            $st = $pdo->prepare("INSERT INTO `gm_user_warning` (`uifd`, `userid`, `reason`, `warn_count`, `room_id`, `created_at`) VALUES (:uifd, :userid, :reason, :warn_count, :room_id, :created_at);");
            $st->execute(
                [
                    ":uifd" => $this->data['uifd'],
                    ":userid" => $this->data['userid'],
                    ":reason" => json_encode([$this->data['warner'] => [$this->data['reason']]]),
                    ":warn_count" => 1,
                    ":room_id" => $this->data['room_id'],
                    ":created_at" => date("Y-m-d H:i:s")
                ]
            );
            print B::sendMessage("{$user} <b>has been warned</b> (<code>1/3</code>)", $this->data['room_id'], null, ['parse_mode'=>'HTML', 'disable_web_page_preview'=>true, "reply_markup"=>json_encode(["inline_keyboard"=>[[["text"=>"Remove warn","callback_data"=> "/nowarn ".$this->data['uifd']]]]])]);
        }
    }
}

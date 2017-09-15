<?php

namespace Bot\Telegram\Traits;

use App\KataBersambung\Group;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait ContractParty
{
    public function party()
    {
        $st1 = new Group($this->room, $this->event['message']['chat']['title'], $this->actor_id, $this->username, $this->actor);
        $this->textReply($st1->open(), null, null, ["parse_mode" => "HTML"]);
    }

    public function join_party()
    {
        $st1 = new Group($this->room, $this->event['message']['chat']['title'], $this->actor_id, $this->username, $this->actor);
        $this->textReply($st1->join(), null, null, ["parse_mode" => "HTML"]);
    }

    public function start_party()
    {
        $st1 = new Group($this->room, $this->event['message']['chat']['title'], $this->actor_id, $this->username, $this->actor);
        $this->textReply($st1->start(), null, null, ["parse_mode" => "HTML", "reply_markup"=>json_encode(["force_reply"=>true,"selective"=>true])]);
    }

    public function parseParty()
    {
        if (isset($this->entities['party'])) {
            foreach ($this->entities['party'] as $key => $val) {
                $st1 = new Group($this->room, $this->event['message']['chat']['title'], $this->actor_id, $this->username, $this->actor);
                $this->textReply($st1->input($val['group_in']), null, null, ["parse_mode" => "HTML", "reply_markup"=>json_encode(["force_reply"=>true,"selective"=>true])]);
            }
        }
    }

    public function end_party()
    {
        $kb = new Handler();
        if ($a = $kb->end_party($this->room, $this->actor_id)) {
            if ($a['status'] == "totally_end") {
                $this->textReply("<b>GAME OVER</b>.\nSelamat buat ".$a['smiter']['nama']." (@".$a['smiter']['username']."), kamu dapat 20 poin. Total {\$total_point}", null, null, array("parse_mode"=>"HTML"));
            } elseif ($a['status'] == "play") {
                $this->textReply(
                    $a['end_user']['name']." (@\\".$a['end_user']['username'].") keluar dari permainan.\n<b>Next</b> ".$a['next_user']['name']." (@".$a['next_user']['username'].")\n\n".strtoupper($a['word'])." <b>".strtoupper($a['rwd'])."</b>",
                    null,
                    null,
                    array("parse_mode"=>"HTML", "reply_markup"=>json_encode(
                        array(
                                   "force_reply"=>true,
                                   "selective"=>true
                                   )
                    ))
                );
            } else {
                $this->textReply("<b>Unknown status</b>\n\nJSON Response :\n".json_encode($a, 128), null, null, array("parse_mode"=>"HTML"));
            }
        } else {
            $this->textReply(
                "false...\n".json_encode($a, 128),
                null,
                $this->event['message']['message_id'],
                array("parse_mode"=>"HTML", "reply_markup"=>json_encode(
                    array(
                                   "force_reply"=>true,
                                   "selective"=>true
                                   )
                ))
            );
        }
    }
}

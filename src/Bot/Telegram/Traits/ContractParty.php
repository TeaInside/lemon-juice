<?php

namespace Bot\Telegram\Traits;

use Bot\Telegram\Games\KataBersambung\Handler;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

 trait ContractParty
 {
 	public function party()
 	{
 		if ($this->type_chat != "private") {
	 		$h = new Handler($this->actor_id, $this->event['message']['from']['username'], $this->actor);
	 		/*
	 			$this->textReply("Sedang dalam perbaikan :3\n\nMohon dibantu https://github.com/ammarfaizi2/lemon-juice",null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		*/
	 		if ($h->openGroup($this->room, $this->actor_id, $this->event['message']['chat']['title'])) {
	 			$this->textReply("Berhasil memulai session !\n\n/join_party untuk join.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		} else {
	 			$this->textReply("Error", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
	 		}
	 	}
 	}

 	public function join_party()
 	{
 		$kb = new Handler($this->actor_id, $this->event['message']['from']['username'], $this->actor);
        if ($a = $kb->user_join($this->actor_id, $this->room) and is_int($a)) {
        	$this->textReply("@".$this->event['message']['from']['username']." (".$this->actor.") berhasil bergabung ke dalam party.\n\nJumlah peserta party, {\$jml_peserta} orang.\n/start_party untuk memulai.\n\n{$a}", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        } elseif ($a === "room_not_found") {
            $this->textReply("Belum ada party, /party untuk memulai !", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        } elseif ($a === "pun_join") {
            $this->textReply("Anda sudah bergabung ke dalam party. /start_party untuk memulai party.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        } else {
            $this->textReply("<b>Error System</b>\n\n".$a, null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        }
 	}

    public function start_party()
    {
        $kb = new Handler();
        if ($a = $kb->start($this->room, $this->actor_id)) {
            switch ($a) {
                case 'ok':
                    $this->textReply("Berhasil memulai party.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                    break;
                case 'kurang_wong':
                    $this->textReply("Kurang anggota party. Minimal 2 orang untuk memulai.\n/join_party untuk join.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                    break;
                case 'belum_join': 
                    $this->textReply("Kamu belum bergabung ke party ini.\n/join_party untuk bergabung.", null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
                    break;
                default:
                    # code...
                    break;
            }
        } else {
            $this->textReply("<b>Error System</b>\n\n".$a, null, $this->event['message']['message_id'], array("parse_mode"=>"HTML"));
        }
    }
 }



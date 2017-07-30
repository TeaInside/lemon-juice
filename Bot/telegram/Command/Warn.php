<?php

namespace Telegram\Command;

use Telegram\B;
use Foundation\DB;

class Warn
{
	public function __construct($userwarn, $room_id, $msg_id, $warner, $uwarn, $wname, $reason = "")
	{
		$this->uwarn = $uwarn;
		$this->wname = $wname;
		$this->userwarn = $userwarn;
		$this->room_id = $room_id;
		$this->warner = $warner;
		$this->reason = trim($reason);
		$this->msg_id = $msg_id;
	}

	public function run()
	{
		if (strpos(B::getChatAdministrators($this->room_id), $this->warner) !== false){
			$get = DB::table("gm_user_warning")->select("warn_count", "reason")->where("uifd", $this->userwarn."|".$this->room_id)->limit(1)->first();
			if ($get !== false) {
				$res = json_decode($get->reason, true);
				$res[][$this->warner] = $this->reason;
				$get = $get->warn_count;
			} else {
				$res = [[$this->warner => $this->reason]];
			}
			$a = ((int) $get) + 1;
			if ($a < 3) {
				print B::sendMessage("{$this->wname} {$this->uwarn} <b>has been warned!</b> (<b>".($a)."/3</b>)", $this->room_id, null, [
					"parse_mode" => "HTML"
				]);
				if ($get === false) {
					DB::table("gm_user_warning")->insert([
							"uifd" => $this->userwarn."|".$this->room_id,
							"userid" => $this->userwarn,
							"reason" => json_encode($res),
							"warn_count" => 1,
							"room_id" => $this->room_id,
							"created_at" => date("Y-m-d H:i:s")
						]);
				} else {
					DB::pdoInstance()->prepare("UPDATE `gm_user_warning` SET `warn_count`= `warn_count`+1, `reason`=:res WHERE `uifd`=:uifd LIMIT 1;")->execute([
							":res" => json_encode($res),
							":uifd" => $this->userwarn."|".$this->room_id
						]);
				}
			} else {
				$a = "{$this->wname} {$this->uwarn} <b>banned</b> : reached the max number of warnings (<b>3/3</b>)";
				print B::sendMessage($a, $this->room_id, null, [
					"parse_mode" => "HTML"
				]);
				DB::pdoInstance()->prepare("UPDATE `gm_user_warning` SET `warn_count`= `warn_count`+1, `reason`=:res WHERE `uifd`=:uifd LIMIT 1;")->execute([
							":res" => json_encode($res),
							":uifd" => $this->userwarn."|".$this->room_id
				]);
			}
		} else {
			print B::deleteMessage($this->msg_id, $this->room_id);
		}
	}
}
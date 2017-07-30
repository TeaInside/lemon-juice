<?php

namespace Telegram\Traits;

use Telegram\B;
use Telegram\Command\Warn;

trait Command
{
	public function command()
	{
		$list_cmd = [
			"/ban" => ["!ban"],
			"/warn" => ["!warn"],
			"/kick" => ["!kick"],
			"/user" => ["!user"],
			"/report" => ["!report"]
		];
		$ex = explode(" ", str_replace("\n", " ", $qw = strtolower($this->text)));
		foreach ($list_cmd as $key => $val) {
			if (in_array($key, $ex)) {
				$a = explode($key, $qw, 2);
				$this->c_param = isset($a[1]) ? $a[1] : "";
				$this->exec($key, $ex);
				break;
			} else {
				foreach ($val as $val) {;
					if (in_array($val, $ex)) {
						$a = explode($val, $qw, 2);
						$this->c_param = isset($a[1]) ? $a[1] : "";
						$this->exec($key, $ex);
						break;
					}
				}
			}
		}
	}

	private function exec($cmd)
	{
		switch ($cmd) {
			case '/warn':
				if ($this->chat_type!="private" && isset($this->reply_to)) {
					$uname = isset($this->reply_to['from']['username']) ? "(@".$this->reply_to['from']['username'].")" : "";
					$st = new Warn($this->reply_to['from']['id'], $this->room, $this->msg_id, $this->actor, $uname, $this->reply_to['from']['first_name'], $this->c_param);
					$st->run();
				} else {
					print B::deleteMessage($this->msg_id, $this->room);
				}
				break;

			case '/report':
				if ($this->chat_type!="private") {
					$uname = isset($this->reply_to['from']['username']) ? "(@".$this->reply_to['from']['username'].")" : "";
					$st = new Warn($this->reply_to['from']['id'], $this->room, $this->msg_id, $this->actor, $uname, $this->reply_to['from']['first_name'], $this->c_param);
					$st->run();
				} else {
					B::deleteMessage($this->msg_id, $this->room);
				}
				break;
			
			default:
				# code...
				break;
		}
	}
}
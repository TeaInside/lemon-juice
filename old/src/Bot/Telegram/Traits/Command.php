<?php

namespace Bot\Telegram\Traits;

use Bot\Telegram\B;

trait Command
{
	/**
	 * @var array
	 */
	private $exploded_message = [];

	/**
	 * Command
	 */
	public function command()
	{
		if ($this->event_type == "text") {
			$command_list = [
				"/ban"  => ["!ban"],
				"/warn" => ["!warn"],
				"/user" => ["!user"],
				"/time" => ["!time"],
				"/whois" => ["!whois", "whois"]
			];
			$this->exploded_message = explode(" ", $this->text);
			foreach ($command_list as $key => $value) {
				if (in_array($key, $this->exploded_message)) {
					if ($this->exec($key)) {
						break;
					}
				}
			}
		}
	}

	/**
	 * Execute command
	 */
	private function exec($cmd)
	{
		$this->bgc = __DIR__."/../bg_controller";
		switch ($cmd) {
			case '/ban':
					shell_exec($this->bgc."/Command/Ban.php ");
				break;
			case '/time':
					B::sendMessage(date("Y-m-d h:i:s A"), $this->room, $this->msg_id);
					return true;
				break;
			case '/ytdl':
			
			default:
					
				break;
		}
	}
}
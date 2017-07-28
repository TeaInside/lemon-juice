<?php

namespace Bot\Telegram\Traits;

trait Command
{
	/**
	 * @var array
	 */
	private $expoded_message = [];

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
				"/time" => ["!time"]
			];
			$this->expoded_message = explode(" ", $this->event_type);
			foreach ($command_list as $key => $value) {
				if (in_array($key, $this->exploded_message) or in_array($val, $this->exploded_message)) {
					$this->exec($key);
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
			
			default:
					
				break;
		}
	}
}
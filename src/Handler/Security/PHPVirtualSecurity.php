<?php

namespace Handler\Security;

trait PHPVirtualSecurity
{
	public function __php_security()
	{
		/**
		 * Harmfull syntax
		 */
		$a = [
			"rm ",
			"eval",
			"include",
			"require",
			"require_once",
			"include_once",
			"python ",
			"system ",
			"exec ",
		];
		$flag = true;
		if (!in_array($this->userid, SUDOERS)) {
			foreach ($a as $val) {
				if (strpos($this->lowertext, $val) !== false) {
					$flag = false;
					break;
				}
			}
		}
		return $flag;
	}
}
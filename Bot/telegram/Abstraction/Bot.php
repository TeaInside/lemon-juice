<?php

namespace Telegram\Abstraction;

abstract class Bot
{
	/**
	 * @param string $func
	 * @param array  $args
	 */
	public static function __callStatic($func, $args)
	{
	}
}
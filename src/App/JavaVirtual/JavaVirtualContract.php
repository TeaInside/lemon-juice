<?php

namespace App\JavaVirtual;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\JavaVirtual
 * @since 0.0.1
 */

interface JavaVirtualContract
{
	/**
	 * Construct.
	 *
	 * @param string $java_code
	 */
	public function __construct($java_code);

	/**
	 * Execute Java Virtual
	 *
	 * @return string
	 */
	public function execute();
}
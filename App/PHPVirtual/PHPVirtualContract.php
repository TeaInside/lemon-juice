<?php

namespace App\PHPVirtual;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\PHPVirtual
 * @since 0.0.1
 */

interface PHPVirtualContract
{	
	/**
	 *
	 * Constructor.
	 *
	 * @param string $php_code
	 *
	 */
	public function __construct($php_code);

	/**
	 * Execute php virtual.
	 *
	 * @return string
	 */
	public function execute();
}
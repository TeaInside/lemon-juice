<?php

namespace App\RubyVirtual;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\RubyVirtual
 * @since 0.0.1
 */

use App\RubyVirtual\RubyVirtualContract;
use App\RubyVirtual\RubyVirtualException;

defined("RUBYVIRTUAL_DIR") or die("RUBYVIRTUAL_DIR not defined!\n");

class RubyVirtual
{
	/**
	 * @var string
	 */
	private $ruby_code;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * Construct.
	 *
	 * @param string $ruby_code
	 */
	public function __construct($ruby_code)
	{
		is_dir(RUBYVIRTUAL_DIR) or mkdir(RUBYVIRTUAL_DIR);
		is_dir(RUBYVIRTUAL_DIR) or shell_exec("mkdir -p ".RUBYVIRTUAL_DIR);
		$this->filename = sha1($ruby_code).".rb";
		$this->ruby_code = $ruby_code;
	}

	/**
	 * Create file.
	 */
	public function create_file()
	{
		if (!file_exists(RUBYVIRTUAL_DIR."/".$this->filename)) {
			$handle = fopen(RUBYVIRTUAL_DIR."/".$this->filename, "w");
			fwrite($handle, $this->ruby_code);
			fclose($handle);
		}
	}

	/**
	 * Execute.
	 */
	public function execute()
	{
		$this->create_file();
		return shell_exec("ruby ".RUBYVIRTUAL_DIR."/".$this->filename." 2>&1");
	}
}

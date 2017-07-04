<?php

namespace App\CVirtual;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\RubyVirtual
 * @since 0.0.1
 */

class CVirtual
{
	/**
	 * @var string
	 */
	private $c_code;

	/**
	 * @var string
	 */
	private $filename;

	/**
	 * @param string $c_code
	 */
	public function __construct($c_code)
	{
		$this->c_code = $c_code;
		$this->filename = sha1($this->c_code).".C";
	}

	/**
	 * Create file.
	 */
	public function create_file()
	{
		if (!file_exists(CVIRTUAL_DIR."/".$this->filename)) {
			$handle = fopen(CVIRTUAL_DIR."/".$this->filename, "w");
			fwrite($handle, $this->c_code);
			fclose($handle);
		}
	}

	/**
	 * Compile
	 */
	private function compile()
	{
		
	}
}
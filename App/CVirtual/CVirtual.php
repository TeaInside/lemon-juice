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
	 * @var string
	 */
	private $compile;

	/**
	 * @param string $c_code
	 */
	public function __construct($c_code)
	{
		$this->c_code = $c_code;
		$this->filename = sha1($this->c_code);
	}

	/**
	 * Create file.
	 */
	public function create_file()
	{
		if ($a = !file_exists(CVIRTUAL_DIR."/".$this->filename.".C")) {
			$handle = fopen(CVIRTUAL_DIR."/".$this->filename.".C", "w");
			fwrite($handle, $this->c_code);
			fclose($handle);
		}
		return $a;
	}

	/**
	 * Compile
	 */
	private function compile()
	{
		$this->compile = shell_exec("cc ".CVIRTUAL_DIR."/".$this->filename.".C -o ".CVIRTUAL_DIR."/".$this->filename." 2>&1");
	}

	/**
	 * Execute.
	 */
	public function execute()
	{
		if ($this->create_file()) {
			$this->compile();
		}
		if ($this->compile) {
			return $this->compile;
		}
		$a = shell_exec(CVIRTUAL_DIR."/".$this->filename." 2>&1");
		$a = empty($a) ? "~" : $a;
		return $a;
	}
}
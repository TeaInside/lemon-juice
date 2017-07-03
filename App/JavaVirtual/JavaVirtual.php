<?php

namespace App\JavaVirtual;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\JavaVirtual
 * @since 0.0.1
 */

use App\JavaVirtual\JavaVirtualContract;
use App\JavaVirtual\JavaVirtualException;

defined("JAVAVIRTUAL_DIR") or die("JAVAVIRTUAL_DIR not defined!\n");

class JavaVirtual implements JavaVirtualContract
{
	/**
	 * @var string
	 */
	private $java_code;

	/**
	 * @var string
	 */
	private $class_name;

	/**
	 * @var string
	 */
	private $app_output;

	/**
	 * Construct.
	 *
	 * @param string $java_code
	 */
	public function __construct($java_code)
	{
		is_dir(JAVAVIRTUAL_DIR) or mkdir(JAVAVIRTUAL_DIR);
		is_dir(JAVAVIRTUAL_DIR) or shell_exec("mkdir -p ".JAVAVIRTUAL_DIR);
		$this->java_code = $java_code;
	}

	/**
	 * Execute Java Virtual
	 *
	 * @return string
	 */
	public function execute()
	{
		$this->getClassName();
		if ($this->class_name === false) {
			return "Error class name !";
		} else {
			$this->create_file();
			$this->compile();
			$this->run();
		}
		return $this->app_output;
	}

	/**
	 * Get class name
	 */
	private function getClassName()
	{	
		$a = explode("class", $this->java_code);
		if (isset($a[1])) {
			$a = explode("{", $a[1]);
			$this->class_name = trim($a[0]);
		} else {
			$this->class_name = false;
		}
	}

	/**
	 * Create java file.
	 */
	private function create_file()
	{
		if (file_exists(JAVAVIRTUAL_DIR."/".$this->class_name.".java")) {
			unlink(JAVAVIRTUAL_DIR."/".$this->class_name.".java");
		}
		if (JAVAVIRTUAL_DIR."/".$this->class_name.".class") {
			unlink(JAVAVIRTUAL_DIR."/".$this->class_name.".class");
		}
		$handle = fopen(JAVAVIRTUAL_DIR."/".$this->class_name.".java", "w");
		fwrite($handle, $this->java_code);
		fclose($handle);
	}

	/**
	 * Compile java.
	 */
	private function compile()
	{
		$this->compile = shell_exec("javac -d ".JAVAVIRTUAL_DIR." ".JAVAVIRTUAL_DIR."/".$this->class_name.".java 2>&1");
	}

	/**
	 * Run java.
	 */	
	private function run()
	{
		if ($this->compile) {
			$this->app_output = $this->compile;
		} else {
			$this->app_output = shell_exec("cd ".JAVAVIRTUAL_DIR." && java ".$this->class_name." 2>&1");
		}
	}
}

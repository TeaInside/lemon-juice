<?php

namespace Lemon;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @since 0.0.1
 * @package Lemon
 * @version 0.0.1
 */

class Curl
{	
	const USERAGENT = "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:46.0) Gecko/20100101 Firefox/46.0";

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var array
	 */
	private $opt = array();

	/**
	 * Constructor.
	 */
	public function __construct($url)
	{
		$this->url = $url;
		$this->opt = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_USERAGENT	   => self::USERAGENT,

			);
	}

	public function post($post)
	{

	}
}
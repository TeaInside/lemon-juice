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
	 * Curl resource.
	 *
	 * @var curl
	 */
	private $ch;

	/**
	 * URL.
	 *
	 * @var string
	 */
	private $url;

	/**
	 * Curl option.
	 *
	 * @var array
	 */
	private $opt = array();

	/**
	 * @var string
	 */
	private $output;

	/**
	 * @var string
	 */
	public $error;

	/**
	 * @var int
	 */
	public $errno;

	/**
	 * @var array
	 */
	public $curl_info;

	/**
	 * Constructor.
	 */
	public function __construct($url)
	{
		$this->ch  = curl_init();
		$this->url = $url;
		$this->opt = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_USERAGENT	   => self::USERAGENT,
			);
	}

	/**
	 * Send method post.
	 *
	 * @param string|array $post
	 * @since 0.0.1
	 */
	public function post($post)
	{
		$this->opt[CURLOPT_POST] = true;
		$this->opt[CURLOPT_POSTFIELDS] = $post;
	}

	/**
	 * Set Cookie Jar and Cookie File
	 *
	 * @param string $cookie Cookie Jar (realpath)
	 * @since 0.0.1
	 */
	public function cookiejar($cookie)
	{
		$this->opt[CURLOPT_COOKIEJAR] = $cookie;
		$this->opt[CURLOPT_COOKIEFILE] = $cookie;
	}

	/**
	 * Execute curl.
	 * @since 0.0.1
	 * @return string
	 */
	public function exec()
	{
		curl_setopt_array($this->ch, $this->opt);
		$this->output = curl_exec($this->ch);
		$this->error  = curl_error($this->ch);
		$this->errno  = curl_errno($this->ch);
		$this->curl_info = curl_getinfo($this->ch);
		return $this->output;
	}
}



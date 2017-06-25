<?php

namespace App\MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\MyAnimeList
 * @since 0.0.1
 */

use Lemon\Curl;
use App\MyAnimeList\MyAnimeListContract;
use App\MyAnimeList\MyAnimeListException;

defined("data") or die("Data not defined !");

class MyAnimeList implements MyAnimeListContract
{
	/**
	 * @var string
	 */
	private $username;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var array
	 */
	private $hash_table = array();

	/**
	 * What are you looking for.
	 *
	 * @var string
	 */
	private $q;

	/**
	 * Anime or Manga ?
	 *
	 * @var string
	 */
	private $type;

	/**
	 * @var string
	 */
	private $current_hash;

	/**
	 * @var string|array
	 */
	private $out;

	/**
	 * Constructor.
	 */
	public function __construct($username, $password)
	{
		is_dir(data) or mkdir(data);
		is_dir(data."/MyAnimeList") or mkdir(data."/MyAnimeList");
		is_dir(data."/MyAnimeList/history") or mkdir(data."/MyAnimeList/history");
		is_dir(data."/MyAnimeList/data") or mkdir(data."/MyAnimeList/data");
		if (!(is_dir(data."/MyAnimeList") && is_dir(data."/MyAnimeList/history") && is_dir(data."/MyAnimeList/data"))) {
			throw new MyAnimeListException("Cannot create directory.", 1);
		}
		if (file_exists(data."/MyAnimeList/hash_table.json")) {
			$this->hash_table = json_decode(file_get_contents(data."/MyAnimeList/hash_table.json"), true);
			$this->hash_table = is_array($this->hash_table) ? $this->hash_table : array();
		} else {
			file_put_contents(data."/MyAnimeList/hash_table.json", json_encode(array()));
			$this->hash_table = array();
		}
		$this->username = $username;
		$this->password = $password;
	}

	/**
	 * @param string $q
	 * @param string $type
	 * @since 0.0.1
	 */
	public function search($q, $type = "anime")
	{
		$this->q = strtolower($q);
		$this->type = $type;
		$this->current_hash = sha1($this->q);
	}

	public function exec()
	{
		$cond = isset($this->hash_table[$this->current_hash]);
		if ($cond) {
			$this->out = json_decode(file_get_contents(data."/MyAnimeList/history/".$this->current_hash), true);
		}
		if (!$cond || !is_array($this->out)) {
			$ch = new Curl("https://myanimelist.net/api/{$this->type}/search.xml?q=".urlencode($this->q));
			$ch->set_opt(array(
					CURLOPT_USERPWD=>"{$this->username}:{$this->password}",
					CURLOPT_CONNECTTIMEOUT=>30
				));
			$out = $ch->exec();
			if (function_exists("simplexml_load_string")) {
                $result = json_encode(simplexml_load_string($out), 128);
                $result=='false' or file_put_contents(data."/MyAnimeList/history/".$this->current_hash, $result);
                $result = json_decode($result, true);
            } else {
                $result = "Cannot load simplexml_load_string";
            }
            $this->out = $result;
		}
	}
}

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
	 * @var array
	 */
	private $hash_table = array();

	/**
	 * Constructor.
	 */
	public function __construct()
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
	}
}

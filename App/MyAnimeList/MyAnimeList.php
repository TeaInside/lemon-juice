<?php

namespace App\MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\MyAnimeList
 * @since 0.0.1
 */

use IceTeaSystem\Curl;
use App\MyAnimeList\MyAnimeListContract;
use App\MyAnimeList\MyAnimeListException;

defined("data") or die("Data not defined !\n");

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
	private $out = null;

	/**
	 * Constructor.
	 * @param string $username
	 * @param string $password
	 * @throws App\MyAnimeList\MyAnimeListException
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

	/**
	 * Execute search.
	 * @throws App\MyAnimeList\MyAnimeListException
	 */
	public function exec()
	{
		$cond = isset($this->hash_table[$this->current_hash]);
		if ($cond) {
			$this->out = json_decode(file_get_contents(data."/MyAnimeList/history/".$this->current_hash), true);
		}
		if (!$cond || !is_array($this->out)) {
			$out = $this->online_search($this->q, $this->type);
			if (function_exists("simplexml_load_string")) {
                $result = json_encode(simplexml_load_string($out), 128);
                $result=='false' or file_put_contents(data."/MyAnimeList/history/".$this->current_hash, $result);
                $result = json_decode($result, true);
                $this->out = $result;
                if ($result!==false) {
                	$this->save_hash($this->get_entry());
                }
            } else {
            	$result = "Function simplexml_load_string is doesn't exists !";
            	throw new MyAnimeListException($result, 1);
            }
            $this->out = $result;
		}
	}

	/**
	 * Get search result.
	 *
	 * @return array
	 */
	public function get_result()
	{
		return $this->out;
	}

	/**
	 * @param array  $entry_list
	 */
	private function save_hash($entry_list)
	{
		$this->hash_table[$this->current_hash] = $entry_list;
		file_put_contents(data."/MyAnimeList/hash_table.json", json_encode($this->hash_table, 128));
	}

	/**
	 * Get entry list.
	 */
	private function get_entry()
	{
		debug_print_backtrace();
		if (isset($this->out['entry']['id'])) {
			return array($this->out['entry']['id']);
		} else {
			$el = array();
			foreach ($this->out['entry'] as $v) {
				$el[] = $v['id'];
			}
			return $el;
		}
	}

	/**
	 * Get Info.
	 *
	 * @param string $id
	 * @param string $type
	 * @return array|string|bool
	 */
	public function get_info($id, $type = "anime")
	{
		$id = (string)trim($id);
		foreach ($this->hash_table as $key => $value) {
			if (array_search($id, $value)!==false) {
				$return = $key;
				break;
			}
		}
		if (!isset($return)) {
			return false;
		} else {
			$a = json_decode(file_get_contents(data."/MyAnimeList/history/".$return), true);
			if (isset($a['entry']['id'])) {
				return $a;
			} else {
				foreach ($a['entry'] as $v) {
					if ($v['id'] == $id) {
						return $v;
						break;
					}
				}
			}
		}
	}

	/**
	 * Simple search
	 *
	 * @param string $q
	 * @param string $type
	 */
	public function simple_search($q, $type = "anime")
	{
		$q = trim(strtolower($q));
		$this->current_hash = sha1($q);
		if (isset($this->hash_table[$this->current_hash])) {
			$a = json_decode(file_get_contents(data."/MyAnimeList/history/".$this->current_hash), true);
			if ($a === null) {
				$this->hash_table[$this->current_hash] = null;
				$a['entry']['id'] = true;
				$a['entry'] = $this->simple_search($q, $type);
			}
		} else {
			if (function_exists("simplexml_load_string")) {
                $a = json_decode(json_encode(simplexml_load_string($this->online_search($q, $type))), true);                
                $result = json_encode($a, 128);
                $result=='false' or file_put_contents(data."/MyAnimeList/history/".$this->current_hash, $result);
                $this->out = $a;
                if ($result!='false') {
					$this->save_hash($this->get_entry());
                }
            } else {
            	$a = "Function simplexml_load_string is doesn't exists !";
            	throw new MyAnimeListException($a, 1);
            }
		}
		if (isset($a['entry']['id'])) {
			return $a['entry'];
		} else {
			return $a['entry'][0];
		}
	}

	/**
	 * Online search.
	 *
	 * @param string $q
	 * @param string $type
	 */
	private function online_search($q, $type = "anime")
	{
		$ch = new Curl("https://myanimelist.net/api/{$type}/search.xml?q=".urlencode($q));
		$ch->set_opt(array(
				CURLOPT_USERPWD=>"{$this->username}:{$this->password}",
				CURLOPT_CONNECTTIMEOUT=>30
			));
		return $ch->exec();
	}
}

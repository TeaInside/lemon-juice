<?php

namespace App\MyAnimeList;

interface MyAnimeListContract
{
	/**
	 * Constructor.
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($username, $password);

	/**
	 * Set search query.
	 *
	 * @param string $q
	 * @param string $type
	 */
	public function search($q, $type = "anime");

	/**
	 * Execute anime search.
	 */
	public function exec();

	/**
	 * Get search results.
	 *
	 * @return array
	 */
	public function get_result();

	/**
	 * Get Info.
	 *
	 * @param string $id
	 * @param string $type
	 */
	public function get_info($id, $type = "anime");
}
<?php

namespace App\MyAnimeList;

interface MyAnimeListContract
{
	/**
	 * Constructor.
	 */
	public function __construct();

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
}
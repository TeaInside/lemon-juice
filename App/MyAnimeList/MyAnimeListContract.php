<?php

namespace App\MyAnimeList;

interface MyAnimeListContract
{
	/**
	 * Constructor.
	 */
	public function __construct();

	/**
	 * Execute anime search.
	 */
	public function exec();
}
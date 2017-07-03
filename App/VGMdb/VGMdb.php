<?php

namespace App\VGMdb;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\VGMdb
 * @since 0.0.1
 */

use IceTeaSystem\Curl;
use App\VGMdb\VGMdbContract;

class VGMdb implements VGMdbContract
{
	/**
	 * API URL
	 */
	const API_URL = "https://vgmdb.info/";

	/**
	 * Constructor.
	 */
	public function __construct()
	{

	}

	/**
	 *
	 * @param string $q
	 * @return string
	 */
	public function seacrh($q)
	{
		$ch = new Curl(self::API_URL."search/".urlencode($q));
		$ch->set_opt(
				CURLOPT_HTTPHEADER => array("Accept: application/json")
			);
		$out = $ch->exec();
		return $out;
	}
}
<?php

namespace App\MyAnimeList;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package App\MyAnimeList
 * @since 0.0.1
 */

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

    /**
     * Simple search
     *
     * @param string $q
     * @param string $type
     */
    public function simple_search($q, $type = "anime");
}

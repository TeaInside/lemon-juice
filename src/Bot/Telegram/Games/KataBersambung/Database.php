<?php

namespace Bot\Telegram\Games\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Games\KataBersambung
 */

use PDO;
use Bot\Telegram\Games\KataBersambung\Contracts\DatabaseContract;

class Database implements DatabaseContract
{
    /**
     * @var PDO
     */
    public $pdo;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->pdo = new PDO(PDO_CONNECT, PDO_USER, PDO_PASS);
    }
}

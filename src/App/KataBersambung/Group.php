<?php

namespace App\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 */

use App\KataBersambung\Session;

class Group
{
    /**
     * @var App\KataBersambung\Session
     */
    private $sess;

    /**
     * Constructor.
     */
    public function __construct($room_id, $room_name, $userid, $uname, $name)
    {
        $this->sess = new Session($room_id, $room_name, $userid, $uname, $name);
    }

    /**
     * @return mixed
     */
    public function open()
    {
        return $this->sess->openGroup();
    }

    /**
     * @return bool
     */
    public function join()
    {
        return $this->sess->join();
    }

    /**
     * @return bool
     */
    public function start()
    {
        return $this->sess->start();
    }

    /**
     * @return string
     */
    public function input($input)
    {
        return $this->sess->input($input);
    }
}

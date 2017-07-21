<?php

namespace Bot\Telegram\Games\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Games\KataBersambung
 */

use PDO;
use Bot\Telegram\Games\KataBersambung\Database;
use Bot\Telegram\Games\KataBersambung\Contracts\SessionContract;

class Session implements SessionContract
{
    /**
     * @var Bot\Telegram\Games\KataBersambung\Database
     */
    private $db;

    /**
     * @var string
     */
    private $room_id;

    /**
     * @var int
     */
    private $count_users = 0;

    /**
     * @param Bot\Telegram\Games\KataBersambung\Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $room_id
     * @param string $type		 (private,group)
     * @param string $starter_id
     * @param string $room_name
     */
    public function make_session($room_id, $type, $starter_id, $room_name = null)
    {
        $rst = $this->db->pdo->prepare("SELECT `kata` FROM `kb_kamus` WHERE `id`= ".(rand(0, 31644))." LIMIT 1;");
        $rst->execute();
        $rst = $rst->fetch(PDO::FETCH_NUM);
        $rst = $rst[0];
        $this->room_id = $room_id;
        $this->count_users = 1;
        #return 
        return $this->db->pdo->prepare("INSERT INTO `kb_session` (`room_id`, `room_name`, `started_at`, `status`, `type`, `users`, `count_users`, `last_word`, `turn`) VALUES (:room_id, :room_name, :started_at, :status, :type, :users, :count_users, :last_word, :turn);")->execute([
                ":room_id" => (int) $room_id,
                ":room_name" => $room_name,
                ":started_at" => date("Y-m-d H:i:s"),
                ":status" => "idle",
                ":type" => $type,
                ":users" => json_encode([$starter_id]),
                ":count_users" => 1,
                ":last_word" => $rst,
                ":turn" => 0
            ]);
    }

    /**
     * Start session.
     * @param string $room_id
     */
    public function session_start($room_id)
    {
        $st = $this->db->pdo->prepare("SELECT `count_users`,`last_word` FROM `kb_session` WHERE `room_id`=:room_id LIMIT 1;");
        $st->execute([":room_id" => $room_id]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st[0] < 2) {
            return false;
        } else {
            $exe = $this->db->pdo->prepare("UPDATE `kb_session` SET `status`='game' WHERE `room_id`=:room_id LIMIT 1;")->execute([
                ":room_id"     => $room_id
            ]);
            return $exe ? $st[1] : false;
        }
    }

    /**
     * @param string $userid
     * @param string $group_id
     */
    public function join($userid, $group_id)
    {
        $st = $this->db->pdo->prepare("SELECT `users`, `count_users` FROM `kb_session` WHERE `room_id`=:group_id LIMIT 1;");
        $st->execute([
                ":group_id" => $group_id
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            $st[0]   = json_decode($st[0], true);
            if (in_array($userid, $st[0])) {
                // already joined
                return false;
            }
            $st[0][] = $userid;
            $st[1]++;
            return $this->db->pdo->prepare("UPDATE `kb_session` SET `users`=:users, `count_users`= {$st[1]} WHERE `room_id`=:group_id LIMIT 1;")->execute([
                    ":users" => json_encode($st[0]),
                    ":group_id" => $group_id
                ]);
        } else {
            return false;
        }
    }

    /**
     * Check group input
     */
    public function check_group_input($group_id, $userid, $input)
    {
        $st = $this->db->pdo->prepare("SELECT `last_word`,`turn`,`count_users` FROM `kb_session` WHERE `room_id`=:group_id LIMIT 1;");
        $st->execute([
                ":group_id" => $group_id
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        $lsc = $this->getLastChar($st[0]);
        $len = strlen($lsc);
        if ($lsc == substr($input, 0, $len)) {
            $stq = $this->db->pdo->prepare("SELECT `id` FROM `kb_kamus` WHERE `kata`=:kata LIMIT 1;");
            $stq->execute([
                    ":kata" => strtolower(trim($input))
                ]);
            if ($stq->fetch(PDO::FETCH_NUM)) {
                $this->input = $input;
                $this->userid = $userid;
                $this->group_id = $group_id;
                $this->next_turn = $st[1]==$st[2] ? 0 : $st[1]+1;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function group_input()
    {
        if ($this->point($this->userid)) {
            $this->db->pdo->prepare("UPDATE `kb_session` SET `last_word`=:last_word, `turn`=:next_turn WHERE room_id=:room_id LIMIT 1;")->execute([
                    ":last_word" => $this->input,
                    ":next_turn" => $this->next_turn,
                    ":room_id" => $this->group_id
                ]);
            return true;
        } else {
            return false;
        }
    }

    public function point($userid, $username = "")
    {
        $st = $this->db->pdo->prepare("SELECT `userid` FROM `kb_point` WHERE `userid`=:userid LIMIT 1;");
        $st->execute([
                ":userid" => $userid
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            $exe = [
                    ":userid" => $userid
                ];
            if (!empty($username)) {
                $op_quer = ", `username`=:username";
                $exe[':username'] = $username;
            } else {
                $op_quer = "";
            }
            return $this->db->pdo->prepare("UPDATE `kb_point` SET `point`= `point`+1{$op_quer} WHERE `userid`=:userid LIMIT 1;")->execute($exe);
        } else {
            return $this->db->pdo->prepare("INSERT INTO `kb_point` (`userid`, `point`, `username`) VALUES (:userid, :pt, :username)")->execute([
                    ":userid" => $userid,
                    ":pt" => 1,
                    ":username" => $username
                ]);
        }
    }

    public function getLastChar($chr)
    {
        $rok = "";
        $sln = strlen($chr);
        $vocal = ["a","i","u","e","o"];
        $vocal_flag = false;
        for ($i=1; $i <= $sln ; $i++) {
            $a = substr($chr, -($i), 1);
            if (in_array($a, $vocal)) {
                if (!$vocal_flag) {
                    $rok .= $a;
                    $vocal_flag = true;
                } else {
                    break;
                }
            } else {
                $rok .= $a;
            }
        }
        return strrev($rok);
    }

    public function session_destroy()
    {
        return $this->db->pdo->prepare("TRUNCATE TABLE `kb_session`")->execute();
    }
}

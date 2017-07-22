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
     * @param string $type       (private,group)
     * @param string $starter_id
     * @param string $room_name
     * @return bool
     */
    public function make_session($room_id, $type, $starter_id, $room_name = null)
    {
        $rst = $this->db->pdo->prepare("SELECT `kata` FROM `kb_kamus` WHERE `id`= ".(rand(0, 31644))." LIMIT 1;");
        $rst->execute();
        $rst = $rst->fetch(PDO::FETCH_NUM);
        $rst = $rst[0];
        $this->room_id = $room_id;
        $this->count_users = 1;
        // return
        $std = $this->db->pdo->prepare("INSERT INTO `kb_session` (`room_id`, `room_name`, `started_at`, `status`, `type`, `users`, `count_users`, `last_word`, `turn`) VALUES (:room_id, :room_name, :started_at, :status, :type, :users, :count_users, :last_word, :turn);");
        $exe = $std->execute(
            [
                ":room_id" => intval($room_id),
                ":room_name" => $room_name,
                ":started_at" => date("Y-m-d H:i:s"),
                ":status" => "idle",
                ":type" => $type,
                ":users" => json_encode([$starter_id]),
                ":count_users" => 1,
                ":last_word" => $rst,
                ":turn" => 0
            ]
        );
        return $exe;
    }

    /**
     * @param string $userid
     * @param string $username
     * @param string $name
     * @return bool
     */
    public function register_user($userid, $username = "", $name = "")
    {
        $st = $this->db->pdo->prepare("SELECT `userid` FROM `kb_user_info` WHERE `userid`=:userid LIMIT 1;");
        $exe = $st->execute([":userid" => $userid]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if (!$st) {
            $st = $this->db->pdo->prepare("INSERT INTO `kb_user_info` (`userid`, `username`, `name`) VALUES (:userid, :username, :name);");
            return $st->execute(
                [
                    ":userid" => $userid,
                    ":username" => $username,
                    ":name" => $name
                ]
            ) ? true : file_put_contents("pdo_error.txt", json_encode($st->errorInfo()));
        } else {
            // registered
            return $exe ? true : json_encode($st->errorInfo());
        }
    }

    /**
     * @param string $room_id
     * @param string $userid
     */
    public function session_start($room_id, $userid)
    {
        $st = $this->db->pdo->prepare("SELECT `count_users`,`last_word`,`users`,`turn` FROM `kb_session` WHERE `room_id`=:room_id LIMIT 1;");
        $st->execute([":room_id" => $room_id]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st[0] < 2) {
            return $st === false ? "room_not_found" : "kurang_wong";
        } else {
            if (strpos($st[2], trim($userid))===false) {
                return "belum_join";
            }
            $exe = $this->db->pdo->prepare("UPDATE `kb_session` SET `status`='game' WHERE `room_id`=:room_id LIMIT 1;")->execute(
                [
                ":room_id"     => $room_id
                ]
            );
            $this->userturn = $this->get_user_info;
            $st[2] = json_decode($st[2], true);
            $ui = $this->get_user_info($st[2][$st[3]]);
            return $exe ? array(
                    "word" => $st[1],
                    "rwd" => $this->getLastChar($st[1]),
                    "username" => $ui['username'],
                    "name" => $ui['name']
                )    : false;
        }
    }

    /**
     * @param string $userid
     */
    public function get_user_info($userid)
    {
        $st = $this->db->pdo->prepare("SELECT `username`, `name` FROM `kb_user_info` WHERE `userid`=:userid LIMIT 1;");
        $st->execute(
            [
                ":userid" => $userid
            ]
        );
        $st = $st->fetch(PDO::FETCH_ASSOC);
        return $st;
    }

    /**
     * @param string $userid
     * @param string $group_id
     */
    public function join($userid, $group_id)
    {
        $st = $this->db->pdo->prepare("SELECT `users`, `count_users` FROM `kb_session` WHERE `room_id`=:group_id LIMIT 1;");
        $st->execute(
            [
                ":group_id" => $group_id
            ]
        );
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            $st[0]   = json_decode($st[0], true);
            if (in_array($userid, $st[0])) {
                // already joined
                return 'pun_join';
            }
            $st[0][] = $userid;
            $st[1]++;
            $stex = $this->db->pdo->prepare("UPDATE `kb_session` SET `users`=:users, `count_users`= {$st[1]} WHERE `room_id`=:group_id LIMIT 1;");
            return  $stex->execute(
                [
                    ":users" => json_encode($st[0]),
                    ":group_id" => $group_id
                ]
            ) ? $st[1] : json_encode(["error"=>true, $stex->errorInfo()]);
        } else {
            return 'room_not_found';
        }
    }

    /**
     * @param string $group_id
     * @param string $userid
     * @param string $input
     */
    public function check_group_input($group_id, $userid, $input)
    {
        $st = $this->db->pdo->prepare("SELECT `last_word`,`turn`,`count_users`,`users` FROM `kb_session` WHERE `room_id`=:group_id LIMIT 1;");
        $st->execute(
            [
                ":group_id" => $group_id
            ]
        );
        $st = $st->fetch(PDO::FETCH_NUM);
        $lsc = $this->getLastChar($st[0]);
        $len = strlen($lsc);
        $users = json_decode($st[3], true);
        if (!in_array($userid, $users)) {
            return "belum_join";
        }
        if ($users[$st[1]] != $userid) {
            return "belum_giliran";
        }
        if ($lsc == substr($input, 0, $len)) {
            $stq = $this->db->pdo->prepare("SELECT `id` FROM `kb_kamus` WHERE `kata`=:kata LIMIT 1;");
            $stq->execute(
                [
                    ":kata" => strtolower(trim($input))
                ]
            );
            if ($stq->fetch(PDO::FETCH_NUM)) {
                $this->input = $input;
                $this->next_turn = $st[1]==($st[2]-1) ? 0 : $st[1]+1;
                $this->userid = $users[$this->next_turn];
                $this->group_id = $group_id;
                return true;
            } else {
                $this->input = $st[0];
                $this->userid = $userid;
                $this->group_id = $group_id;
                $this->next_turn = $st[1];
                return null;
            }
        } else {
            $this->input = $st[0];
            $this->userid = $userid;
            $this->group_id = $group_id;
            $this->next_turn = $st[1];
            return false;
        }
    }

    public function group_input()
    {
        if ($this->point($this->userid)) {
            $this->db->pdo->prepare("UPDATE `kb_session` SET `last_word`=:last_word, `turn`=:next_turn WHERE room_id=:room_id LIMIT 1;")->execute(
                [
                    ":last_word" => $this->input,
                    ":next_turn" => $this->next_turn,
                    ":room_id" => $this->group_id
                ]
            );
            $ui = $this->get_user_info($this->userid);
            return array(
                    "salah" => 0,
                    "word" => $this->input,
                    "rwd" => $this->getLastChar($this->input),
                    "username" => $ui['username'],
                    "name" => $ui['name']
                );
        } else {
            return false;
        }
    }

    public function wrong_group_input()
    {
        $ui = $this->get_user_info($this->userid);
        return array(
                "salah" => 1,
                "word" => $this->input,
                "rwd" => $this->getLastChar($this->input),
                "username" => $ui['username'],
                "name" => $ui['name']
            );
    }

    /**
     * @param string $room_id
     */
    public function turn_gr($room_id)
    {
        $st = $this->db->pdo->prepare("SELECT `users`,`turn` FROM `kb_session` WHERE `room_id`=:group_id LIMIT 1;");
        $st->execute(
            [
                ":group_id" => $group_id
            ]
        );
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            $w = json_decode($st[0], true);
            return $w[$st[1]];
        } else {
        }
    }

    public function point($userid, $username = "")
    {
        $st = $this->db->pdo->prepare("SELECT `userid` FROM `kb_point` WHERE `userid`=:userid LIMIT 1;");
        $st->execute(
            [
                ":userid" => $userid
            ]
        );
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
            return $this->db->pdo->prepare("INSERT INTO `kb_point` (`userid`, `point`, `username`) VALUES (:userid, :pt, :username)")->execute(
                [
                    ":userid" => $userid,
                    ":pt" => 1,
                    ":username" => $username
                ]
            );
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
                if ($vocal_flag) {
                    break;
                }
            }
        }
        return strrev($rok);
    }

    public function session_destroy()
    {
        return $this->db->pdo->prepare("TRUNCATE TABLE `kb_session`")->execute();
    }

    public function end_party($room, $userid)
    {
        $st = $this->db->pdo->prepare("SELECT `users`,`turn`,`last_word` FROM `kb_session` WHERE `room_id`=:room_id LIMIT 1;");
        $st->execute(
            [
                ":room_id" => $room
            ]
        );
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            $st[0] = json_decode($st[0], true);
            if (in_array($userid, $st[0])) {
                $keyer = array();
                $smiter = null;
                foreach ($st[0] as $key => $val) {
                    if ($val == $userid) {
                        if ($key == 0) {
                            $smiter = $st[0][count($st[0])-1];
                        } else {
                            $smiter = $st[0][$key-1];
                        }
                        $keyer[] = $key;
                        unset($st[0][$key]);
                    }
                }
                $count = count($st[0]);
                $mui = $this->get_user_info($smiter);
                if ($count > 0) {
                    $st[1] = in_array($st[1], $keyer) ? ($st[1]==($count-1) ? 0 : $st[1]++) : $st[1];
                    $this->db->pdo->prepare("UPDATE `kb_session` SET `users`=:users, `turn`=:turn, `count_users`=:count_users WHERE `room_id`=:room_id LIMIT 1;")->execute(
                        [
                            ":users" => json_encode($st[0]),
                            ":turn" => $st[1],
                            ":count_users" => $count,
                            ":room_id" => $room
                        ]
                    );
                    $ui = $this->get_user_info($st[0][$st[1]]);
                    return array(
                            "status" => "play",
                            "word" => $st[2],
                            "rwd" => $this->getLastChar($st[2]),
                            "next_turn" => [
                                "username" => $ui['username'],
                                "name" => $ui['name']
                            ],
                            "smiter" => [
                                "username" => $mui['username'],
                                "name" => $mui['name']
                            ]
                        );
                } else {
                    $this->db->pdo->prepare("DELETE FROM `kb_session` WHERE `room_id`=:room_id LIMIT 1;")->execute(
                        [
                            ":room_id" => $room
                        ]
                    );
                    return array(
                            "status" => "end_totally",
                            "smiter" => [
                                "username" => $mui['username'],
                                "name" => $mui['name']
                            ]
                        );
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

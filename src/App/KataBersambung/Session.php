<?php

namespace App\KataBersambung;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 */

use PDO;

class Session
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var int
     */
    private $room_id;

    /**
     * @var string
     */
    private $room_name;

    /**
     * @var int
     */
    private $userid;

    /**
     * @var string
     */
    private $uname;

    /**
     * @var string
     */
    private $name;

    /**
     * Constructor.
     * @param int 	 $room_id
     * @param string $room_name
     * @param int	 $userid
     * @param string $uname
     * @param string $name
     */
    public function __construct($room_id, $room_name, $userid, $uname = "", $name = "")
    {
        $this->pdo = new PDO(PDO_CONNECT, PDO_USER, PDO_PASS);
        $this->room_id = $room_id;
        $this->room_name = $room_name;
        $this->userid = $userid;
        $this->uname = $uname;
        $this->name = $name;
        $this->save_user_info();
    }

    /**
     * @return mixed
     */
    public function openGroup()
    {
        $st = $this->pdo->prepare("SELECT `status` FROM `kb_room` WHERE `room_id`=:room_id LIMIT 1;");
        $exe = $st->execute([
                ":room_id" => $this->room_id
            ]);
        if ($exe) {
            $r = $st->fetch(PDO::FETCH_NUM);
            if ($r === false || $r[0] == "off") {
                $st = $this->pdo->prepare("INSERT INTO `kb_room` (`room_id`, `room_name`, `status`, `created_at`, `expired_at`, `participants`, `turn`, `last_word`, `count_users`) VALUES (:room_id, :room_name, :status, :created_at, :expired_at, :participants, :turn, :last_word, :count_users);");
                $t = time();
                $exe = $st->execute([
                        ":room_id" => $this->room_id,
                        ":room_name" => $this->room_name,
                        ":status" => "idle",
                        ":created_at" => date("Y-m-d H:i:s", $t),
                        ":expired_at" => date("Y-m-d H:i:s", $t+90),
                        ":participants" => json_encode([$this->userid]),
                        ":turn" => rand(0, 1),
                        ":last_word" => null,
                        ":count_users" => 1
                    ]);
                $this->createUserSession();
                $player = "";

                return $exe ? "Gunakan /join_party untuk bergabung, /start_party untuk memulai.\n<b>Reply</b> pesan dari BOT untuk menjawab.\nWaktumu kurang dari 90 dtk.\n\nPlayer yang siap bermain:\n- <b>".$this->name."</b> (@".$this->uname.")\n=================" : $st->errorInfo();
            } else {
                return $r[0];
            }
        } else {
            return $st->errorInfo();
        }
    }

    /**
     * Join game.
     */
    public function join()
    {
        if (!$this->isJoined()) {
            $st = $this->pdo->prepare("INSERT INTO `kb_user_session` (`userid`, `room_id`, `live`, `expired`) VALUES (:userid, :room_id, :live, :expired);");
            $st->execute([
                    ":userid" => $this->userid,
                    ":room_id" => $this->room_id,
                    ":live" => 3,
                    ":expired" => null
                ]);
            $st = $this->pdo->prepare("SELECT `participants` FROM `kb_room` WHERE `room_id`=:room_id LIMIT 1;");
            $st->execute([
                    ":room_id" => $this->room_id
                ]);
            $st = $st->fetch(PDO::FETCH_NUM);
            if ($st === false) {
                return "group_gak_ada";
            } else {
                $st[0] = json_decode($st[0], true);
                $st[0][] = $this->userid;
                $sta = $this->pdo->prepare("UPDATE `kb_room` SET `participants`=:par, `count_users`=`count_users`+1 WHERE `room_id`=:room_id LIMIT 1;");
                $par = "";
                foreach ($st[0] as $val) {
                	$u = $this->getUserInfo($val);
                	$par.= " - <b>".$u['name']."</b> (@".$u['username'].")\n";
                }
                return $sta->execute([
                        ":par" => json_encode($st[0]),
                        ":room_id" => $this->room_id
                    ]) ? "Gunakan /join_party untuk bergabung, /start_party untuk memulai.\n<b>Reply</b> pesan dari BOT untuk menjawab.\nWaktumu kurang dari 90 dtk.\n\nPlayer yang siap bermain:\n".$par."\n=================" : json_encode($sta->errorInfo());
            }
        } else {
            return false;
        }
    }

    /**
     * Start the game.
     */
    public function start()
    {
        $st = $this->pdo->prepare("SELECT `status`,`participants`,`count_users`,`turn`,`last_word` FROM `kb_room` WHERE `room_id`=:room_id LIMIT 1;");
        $st->execute([
                ":room_id" => $this->room_id
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            if ($st[2] < 2) {
                return "Pemain kurang. /join untuk bergabung";
            }
            $st[1] = json_decode($st[1], true);
            if (!$this->isJoined()) {
                return "Anda belum bergabung ke permainan.  untuk bergabung!";
            } else {
                if ($st[0] == "idle") {
                    $stq = $this->pdo->prepare("SELECT `kata` FROM `kb_kamus` WHERE `id`=".rand(1, 31644)." LIMIT 1");
                    $stq->execute();
                    $wd = $stq->fetch(PDO::FETCH_NUM);
                    $tr = $st[1][$st[3]];
                    $st = $this->pdo->prepare("UPDATE `kb_room` SET `status`=:status,`last_word`=:last_word WHERE `room_id`=:room_id LIMIT 1;");
                    $st->execute([
                            ":status" => 'game',
                            ":last_word" => $wd[0],
                            ":room_id" => $this->room_id
                        ]);
                    $u = $this->getUserInfo($tr);
                    return "#katabersambung\n\nMulai: ".strtoupper($wd[0])."\n<b>".(self::getLastChar($wd[0]))."...</b>\nSekarang <b>".$u['name']."</b> (@".$u['username'].") Reply untuk jawab.";
                } elseif ($st[0] == "off") {
                    return "Belum ada sesi";
                } else {
                	$tr = $st[1][$st[3]];
                	$u = $this->getUserInfo($tr);
                    return "#katabersambung\n\nMulai: ".strtoupper($wd[0])."\n<b>".(strtoupper(self::getLastChar($wd[0])))."...</b>\nSekarang <b>".$u['name']."</b> (@".$u['username'].") Reply untuk jawab.";
                }
            }
        } else {
            return "Belum ada sesi";
        }
    }

    /**
     * @param string $input
     */
    public function input($input)
    {
        $input = strtolower(trim(preg_replace("#[^[:print:]]#", "", $input)));
        $st = $this->pdo->prepare("SELECT `participants`,`turn`,`last_word`,`status`,`count_users`,`cycle` FROM `kb_room` WHERE `room_id`=:room_id LIMIT 1;");
        $st->execute([
                ":room_id" => $this->room_id
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st === false || $st[3] == "off") {
            return "Gak ada sesi";
        } else {
            $st[0] = json_decode($st[0], true);
            if (!$this->isJoined()) {
                return "Anda belum bergabung ke permainan. /join untuk bergabung!";
            } else {
                if (!in_array($this->userid, $st[0])) {
                    return "Sabar, nunggu selesai dulu baru main :p";
                }
                if ($st[0][$st[1]] != $this->userid) {
                    return "Bukan giliranmu";
                } else {
                    $lscr = self::getLastChar($st[2]);
                    $len = strlen($lscr);
                    if ($lscr == substr($input, 0, $len)) {
                        // check kamus
                        $std = $this->pdo->prepare("SELECT `kata` FROM `kb_kamus` WHERE `kata`=:kata LIMIT 1;");
                        $std->execute([
                                ":kata" => $input
                            ]);
                        if ($std->fetch(PDO::FETCH_ASSOC)) {
                            $this->upPoint();
                            $std = $this->pdo->prepare("UPDATE `kb_room` SET `turn`=:turn,`last_word`=:last_word WHERE `room_id`=:room_id LIMIT 1;");
                            $turn = $st[1] == $st[4]-1 ? 0 : $st[1]+1;
                            $std->execute([
                                    ":turn" => $turn,
                                    ":last_word" => $input,
                                    ":room_id" => $this->room_id
                                ]);
                            $u = $this->getUserInfo($st[0][$turn]);
                            $this->addRoomCycle();
                            return "<b>".strtoupper(self::getLastChar($input))."...</b>\nSekarang ".$u['name']." (@".$u['username'].") Reply untuk jawab.";
                        } else {
                            $live = $this->getLive();
                            $this->downLive();
                            if ($live == 1) {
                                $u = $this->getUserInfo();
                            } else {
                                $u = $this->getUserInfo();
                                return "#katabersambung\n\n👎 salah <b>".strtoupper($lscr)."...</b>\nSekarang ".$u['name']." (@".$u['username'].")\nKamu punya ".$live." kesempatan lagi. Reply untuk jawab.";
                            }
                        }
                    } else {
                        $u = $this->getUserInfo();
                        $live = $this->getLive();
                        $this->downLive();
                        if ($live == 0) {
                            $participants = [];
                            unset($st[0][$st[1]]);
                            foreach ($st[0] as $val) {
                                $participants[] = $val;
                            }
                            if (count($participants) > 1) {
                                if ($st[5] > 0) {
                                    $cntr = count($participants)-1;
                                    if ($st[1] == 0) {
                                        $sm = $participants[$cntr];
                                        $turn = 1;
                                    } else {
                                        $tr = $st[1]-1;
                                        $sm = $participants[$tr];
                                        $turn = $tr == $cntr ? 0 : $tr+1;
                                    }
                                } else {
                                    $turn = $st[1];
                                }
                                $pst = $this->pdo->prepare("UPDATE `kb_room` SET `participants`=:par,`count_users`=`count_users`-1,`turn`=:turn WHERE `room_id`=:room_id LIMIT 1;");
                                $pst->execute([
                                        ":par" => json_encode($participants),
                                        ":room_id" => $this->room_id,
                                        ":turn" => $turn
                                ]);
                                if (isset($sm)) {
                                    $this->upPoint(20);
                                    $u = $this->getUserInfo($sm);
                                    return "#katabersambung\n\nKamu gagal menjawab tantangan dari ".$u['name']. "(@".$u['username'].").\n".$u['name']. "(@".$u['username'].") mendapatkan 20 point, total point ".($u['point']);
                                } else {
                                    $u = $this->getUserInfo($participants[$turn]);
                                    return "#katabersambung\n\nKamu gagal menjawab tantangan dari moderator.\nSekarang ".trim($u['name'])." (@".$u['username'].")\n";
                                }
                            }
                            $this->upPoint(30);
                            $u = $this->getUserInfo($participants[0]);
                            $this->truncateSession();
                            return "GAME OVER\nPemain yang tersisa ".$u['name']." (@".$u['username'].").\n".$u['name']." (@".$u['username'].") mendapat tambahan 30 point karena berhasil bertahan.";
                        } else {
                            return "👎 salah <b>".strtoupper($lscr)."...</b>\nSekarang ".$u['name']." (@".$u['username'].")\nKamu punya ".$live." kesempatan lagi. Reply untuk jawab.";
                        }
                    }
                }
            }
        }
    }

    private function truncateSession()
    {
        $this->pdo->prepare("DELETE FROM `kb_user_session` WHERE `room_id`=:room_id;")->execute([
                ":room_id" => $this->room_id
            ]);
        $this->pdo->prepare("DELETE FROM `kb_room` WHERE `room_id`=:room_id LIMIT 1;")->execute([
                ":room_id" => $this->room_id
            ]);
    }

    private function addRoomCycle()
    {
        return $this->pdo->prepare("UPDATE `kb_room` SET `cycle`=`cycle`+1 WHERE `room_id`=:room_id LIMIT 1;")->execute([
                ":room_id" => $this->room_id
            ]);
    }

    private function getLive()
    {
        $st = $this->pdo->prepare("SELECT `live` FROM `kb_user_session` WHERE `userid`=:userid AND `room_id`=:room_id LIMIT 1;");
        $st->execute([
                ":userid" => $this->userid,
                ":room_id" => $this->room_id
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        return isset($st[0]) ? (string) ($st[0]-1) : false;
    }

    private function getUserInfo($userid = null)
    {
        $st = $this->pdo->prepare("SELECT `username`, `name`, `point` FROM `kb_user_info` WHERE `userid`=:userid LIMIT 1;");
        $st->execute([
                ":userid" => ($userid === null ? $this->userid : $userid)
            ]);
        return $st->fetch(PDO::FETCH_ASSOC);
    }

    private function downLive()
    {
        return $this->pdo->prepare("UPDATE `kb_user_session` SET `live`=`live`-1 WHERE `userid`=:userid AND `room_id`=:room_id LIMIT 1;")->execute([
                ":userid" => $this->userid,
                ":room_id" => $this->room_id
            ]);
    }

    private function upPoint($point = 1)
    {
        $point = (int) $point;
        return $this->pdo->prepare("UPDATE `kb_user_info` SET `point`=`point`+{$point} WHERE `userid`=:userid LIMIT 1;")->execute([
                ":userid" => $this->userid
            ]);
    }

    private function gameOver()
    {
        "<b>GAME OVER</b>.\nSelamat buat ".$a['smiter']['nama']." (@".$a['smiter']['username']."), kamu dapat 20 poin. Total {\$total_point}";
    }

    /**
     * Create user session.
     */
    private function createUserSession()
    {
        $st = $this->pdo->prepare("SELECT `userid` FROM `kb_user_session` WHERE `userid`=:userid LIMIT 1;");
        $st->execute([
                ":userid" => $this->userid
            ]);
        if ($st->fetch(PDO::FETCH_NUM) === false) {
            $st = $this->pdo->prepare("INSERT INTO `kb_user_session` (`userid`, `room_id`, `live`, `expired`) VALUES (:userid, :room_id, :live, :expired);");
            $exe = $st->execute([
                    ":userid" => $this->userid,
                    ":room_id" => $this->room_id,
                    ":live" => 3,
                    ":expired" => null
                ]);
            return $exe;
        }
    }

    /**
     * Check joined
     */
    private function isJoined()
    {
        $st = $this->pdo->prepare("SELECT `userid` FROM `kb_user_session` WHERE `userid`=:userid AND `room_id`=:room_id LIMIT 1;");
        $st->execute([
                ":userid" => $this->userid,
                ":room_id" => $this->room_id
            ]);
        return $st->fetch(PDO::FETCH_NUM) === false ? false : true;
    }

    /**
     * @return bool
     */
    private function save_user_info()
    {
        $st = $this->pdo->prepare("SELECT `username`, `name` FROM `kb_user_info` WHERE `userid`=:userid LIMIT 1;");
        $exe = $st->execute([
                ":userid" => $this->userid
            ]);
        $st = $st->fetch(PDO::FETCH_NUM);
        if ($st) {
            // update user info
            if ($st[0] != $this->uname || $st[1] != $this->name) {
                $st = $this->pdo->prepare("UPDATE `kb_user_info` SET `username`=:user, `name`=:name WHERE `userid`=:userid LIMIT 1;");
                $exe = $st->execute([
                        ":user" => $this->uname,
                        ":name" => $this->name,
                        ":userid" => $this->userid
                    ]);
            }
            return $exe;
        } else {
            // insert
            $st = $this->pdo->prepare("INSERT INTO `kb_user_info` (`userid`, `username`, `name`, `registered_at`, `point`) VALUES (:userid, :username, :name, :registered_at, :pt);");
            return $st->execute([
                    ":userid" => $this->userid,
                    ":username" => $this->uname,
                    ":name" => $this->name,
                    ":registered_at" => date("Y-m-d H:i:s"),
                    ":pt" => 0
                ]);
        }
    }

    /**
     * @param string
     */
    public static function getLastChar($chr)
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

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->pdo = null;
    }
}

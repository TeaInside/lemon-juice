<?php

namespace Handler\Command;

use DB;
use PDO;
use Telegram as B;

trait CMDTrait
{	
	private function __warn($reason = null)
	{
		$flag = false;
        $a = json_decode(B::getChatAdministrators([
                "chat_id" => $this->chatid
            ], "GET")['content'], true);
        foreach ($a['result'] as $val) {
            if ($val['user']['id'] == $this->userid) {
                if ($val['can_restrict_members'] || $val['status']=="creator") {
                    $flag = true;
                }
                break;
            }
        }
        if ($flag){
        	$sq = DB::prepare("SELECT `max_warn` FROM `a_known_groups` WHERE `group_id`=:grid LIMIT 1;");
    		$exe = $sq->execute([
    				":grid" => $this->chatid
    			]);
    		if (!$exe) {
    			var_dump($st->errorInfo());
    			die(1);
    		}
    		$sq = $sq->fetch(PDO::FETCH_NUM);
        	$uniq = $this->replyto['from']['id']."-".$this->chatid;
        	$st = DB::prepare("SELECT `warn_count`,`reasons` FROM `user_warning` WHERE `uniq_id`=:uniq LIMIT 1;");
        	$exe = $st->execute([
        			":uniq" => $uniq
        		]);
        	if (!$exe) {
        		var_dump($st->errorInfo());
        		die(1);
        	}
        	if ($st = $st->fetch(PDO::FETCH_NUM)){
        		$se = DB::prepare("UPDATE `user_warning` SET `warn_count`=`warn_count`+1,`reasons`=:rr,`updated_at`=:up WHERE `uniq_id`=:uniq LIMIT 1;");
        		$st[1] = json_decode($st[1], true);
        		$st[1][] = ["warned_by"=>$this->userid,"reason"=>$reason,"warned_at"=>date("Y-m-d H:i:s")];
        		$exe = $se->execute([
        				":rr" => json_encode($st[1]),
        				":uniq" => $uniq,
        				":up" => date("Y-m-d H:i:s")
        			]);
        		if (!$exe) {
        			var_dump($se->errorInfo());
        			die(1);
        		}
        		if ($sq[0] >= $st[0]+1) {
        			$a = B::kickChatMember(
	                    [
	                        "chat_id" => $this->chatid,
	                        "user_id" => $this->replyto['from']['id']
	                    ]
                	);
                	return B::sendMessage([
                			"chat_id" => $this->chatid,
                			"text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>banned:</b> reached the max number of warnings (<code>".($st[0]+1)."/".$sq[0]."</code>)",
                			"parse_mode" => "HTML"
                		]);
        		} else {
        			return B::sendMessage([
                			"chat_id" => $this->chatid,
                			"text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>has been warned</b> (<code>".($st[0]+1)."/".$sq[0]."</code>)",
                			"parse_mode" => "HTML"
                		]);
        		}
        	} else {
        		$st = DB::prepare("INSERT INTO `user_warning` (`userid`,`group_id`,`uniq_id`,`reasons`,`warn_count`,`created_at`,`updated_at`) VALUES (:userid, :group_id, :uniq_id, :reasons, 1, :created_at, null);");
        		$exe = $st->execute([
        				":userid" => $this->replyto['from']['id'],
        				":group_id" => $this->chatid,
        				":uniq_id" => $uniq,
        				":reasons" => json_encode([["warned_by"=>$this->userid,"reason"=>$reason,"warned_at"=>date("Y-m-d H:i:s")]]),
        				":created_at" => date("Y-m-d H:i:s")
        			]);
        		if (!$exe) {
        			var_dump($st->errorInfo());
        			die(1);
        		}
        		return B::sendMessage([
                			"chat_id" => $this->chatid,
                			"text" => "<a href=\"tg://user?id=".$this->replyto['from']['id']."\">".$this->replyto['from']['first_name']."</a> <b>has been warned</b> (<code>1/".$sq[0]."</code>)",
                			"parse_mode" => "HTML"
                		]);
        	}
        } else {
            return B::sendMessage([
                    "chat_id" => $this->chatid,
                    "text" => "You are not allowed to use this command !",
                    "reply_to_message_id" => $this->msgid
                ]);
        }
	}

	private function __ban()
	{
		$flag = false;
        $a = json_decode(B::getChatAdministrators([
                "chat_id" => $this->chatid
            ], "GET")['content'], true);
        foreach ($a['result'] as $val) {
            if ($val['user']['id'] == $this->userid) {
                if ($val['can_restrict_members'] || $val['status']=="creator") {
                    $flag = true;
                }
                break;
            }
        }
        if ($flag){
            if (isset($this->replyto['from']['username']) && strtolower($this->replyto['from']['username']) === strtolower(BOT_USERNAME)) {
                return B::sendMessage([
                        "chat_id" => $this->chatid,
                        "text" => "<b>Error</b> : \n<pre>Bad Request: user is a bot</pre>",
                        "parse_mode" => "HTML",
                        "reply_to_message_id" => $this->msgid
                    ]);
            } else {
                $a = B::kickChatMember(
                    [
                        "chat_id" => $this->chatid,
                        "user_id" => $this->replyto['from']['id']
                    ]
                );
                if ($a['content'] == '{"ok":true,"result":true}') {
                    return B::sendMessage([
                            "text" => '<a href="tg://user?id='.$this->userid.'">'.$this->actorcall.'</a> banned <a href="tg://user?id='.$this->replyto['from']['id'].'">'.$this->replyto['from']['first_name']."</a>!",
                            "chat_id" => $this->chatid,
                            "parse_mode" => "HTML"
                        ]);
                } else {
                    return B::sendMessage([
                        "chat_id" => $this->chatid,
                        "text" => "<b>Error</b> : \n<pre>".htmlspecialchars(json_decode($a['content'], true)['description'])."</pre>",
                        "parse_mode" => "HTML",
                        "reply_to_message_id" => $this->msgid
                    ]);    
                }
            }
        } else {
            return B::sendMessage([
                    "chat_id" => $this->chatid,
                    "text" => "You are not allowed to use this command !",
                    "reply_to_message_id" => $this->msgid
                ]);
        }
        return false;
	}
}
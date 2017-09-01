<?php

namespace Handler\Command;

trait Command
{
    private function __command()
    {
        $__command_list = [
            "/start"  => ["!start", "~start"],
            "/time"   => ["!time", "~time"],
            "/report" => ["!report", "~report"],
            "/kick"   => ["!kick", "~kick"],
            "/ban"    => ["!ban", "~ban"],
            "/unban"  => ["!unban", "~unban"],
            "/nowarn" => ["!nowarn", "~nowarn"],
            "/warn"   => ["!warn", "~nowarn"]
        ];
        foreach ($__command_list as $key => $val) {
            if (!$this->__do_command($key)) {
                foreach ($val as $vax) {
                    $this->__do_command($key);
                    break;
                }
            } else {
                break;
            }
        }
    }

    private function __do_command($command)
    {
        switch ($command) {
        case '/start':
            return B::sendMessage(
                [
                        "text" => "Sedang dalam perbaikan. Laporkan masalah ke @DeadInsideGroup!",
                        "chat_id" => $this->chatid,
                        "reply_to_message_id" => $this->msgid,
                    ]
            );
                break;
        }
    }
}

<?php

namespace Bot\Telegram\Traits;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram\Traits
 * @since 0.0.1
 */

trait MessageBuilder
{
    /**
     * Builder : Balasan text
     *
     * @param string $text      Text
     * @param int    $to        Chat ID
     * @param int    $reply_to  Message ID
     * @param string $option    JSON Sereliazed
     */
    private function textReply($text, $to=null, $reply_to=null, $option=null)
    {
        $this->reply[] = array(
                "type"=>"text",
                "reply_to"=>$reply_to,
                "to"=>($to===null?$this->room:$to),
                "content"=>$text,
                "option"=>$option
            );
    }

    /**
     * Builder : Balasan gambar
     *
     * @param string $imageUrl  URL to image
     * @param int    $to        Chat ID
     * @param int    $reply_to  Message ID
     * @param string $option    JSON Sereliazed
     */
    private function imageReply($imageUrl, $to=null, $reply_to=null, $option=null)
    {
        $this->reply[] = array(
                "type"=>"image",
                "reply_to"=>$reply_to,
                "to"=>($to===null?$this->room:$to),
                "content"=>$imageUrl,
                "option"=>$option
            );
    }
}

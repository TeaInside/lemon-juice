<?php

namespace Stack\LINE;

/**
 * https://api.line.me/v2/bot/profile/{$userid}
 * @author Ammar Faizi
 */
use IceTeaSystem\Curl;

class LINE
{
    /**
     * @var string
     */
    private $channel_token;

    /**
     * @var string
     */
    private $channel_secret;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $post = [];

    /**
     * Constructor.
     * @param string $channel_token
     * @param string $channel_secret
     */
    public function __construct($channel_token, $channel_secret)
    {
        $this->headers = [
            "Content-Type: application/json",
            "Authorization: Bearer ".$channel_token
        ];
    }

    public function buildMessage($to)
    {
        $this->url =  "https://api.line.me/v2/bot/message/push";
        $this->post = [
                "to" => $to,
                "messages" => []
            ];
    }


    /**
     * @param string|array $text
     * @param string       $to
     * @param string       $reply
     */
    public function textMessage($text)
    {
        $this->post['messages'][] = [
                            "type" => "text",
                            "text" => $text
                        ];
    }

    public function exec($op = null)
    {
        $this->_exec($this->url, $this->post, $op);
    }

    /**
     * @param string $userID
     * @return string
     */
    public function getUserInfo($userId)
    {
        return $this->_exec("https://api.line.me/v2/bot/profile/".$userId);
    }

    private function _exec($url, $post = null, $op = null)
    {
        $ch = new Curl($url);
        $opt = [
                CURLOPT_BINARYTRANSFER => true,
                CURLOPT_HTTPHEADER => $this->headers,
                // CURLOPT_HEADER => true
            ];
        if ($post) {
            $opt[CURLOPT_CUSTOMREQUEST] = "POST";
            $opt[CURLOPT_POSTFIELDS] = json_encode($post);
        }
        if (is_array($op)) {
            foreach ($op as $key => $val) {
                $opt[$key] = $val;
            }
        }
        $ch->set_opt($opt);
        $out = $ch->exec();
        return $out;
    }
}

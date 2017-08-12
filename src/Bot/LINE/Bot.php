<?php

namespace Bot\LINE;

use AI\AI;
use Sys\Hub\Singleton;
use Stacks\LINE\LINE as LINEStack;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\LINE
 * @since 0.0.1
 */

class Bot
{
    use Singleton;

    /**
     * @var Stack\LINE\LINE
     */
    private $line;

    /**
     * @var string
     */
    private $webhook_input;

    /**
     * @var bool
     */
    private $reply = false;

    /**
     * @var string
     */
    private $actor;

    /**
     * Constructor.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public function __construct($channel_token, $channel_secret)
    {
        $this->line = new LINEStack($channel_token, $channel_secret);
    }

    /**
     * Run.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public static function run($channel_token, $channel_secret)
    {
        $self = self::getInstance($channel_token, $channel_secret);
        $self->getEvent();
        $self->execute();
        // $self->logs();
    }

    /**
     * Get webhook event
     */
    public function getEvent()
    {
        // $this->webhook_input = '{"events":[{"type":"message","replyToken":"8c0de2f888ef4445b1d03ec4174203bb","source":{"userId":"U547ba62dc793c6557abbb42ab347f15f","type":"user"},"timestamp":1499414643059,"message":{"type":"text","id":"6351180096433","text":"ask penemu lampu"}}]}';
        $this->webhook_input = file_get_contents("php://input");
    }

    /**
     * Execute
     */
    public function execute()
    {
        $events = json_decode($this->webhook_input, true);
        if (isset($events['events'])) {
            $events = $events['events'];
            foreach ($events as $val) {
                $this->event = $val;
                $this->parseEvent();
                if ($this->type == "text") {
                    $this->parseReply();
                    if ($this->reply) {
                        $this->line->exec();
                    }
                }
            }
        }
    }

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $event;

    /**
     * Parse Event
     */
    private function parseEvent()
    {
        if (isset($this->event['message']['text'])) {
            $this->type = "text";
            $this->room = isset($this->event['source']['groupId']) ?  $this->event['source']['groupId'] : $this->event['source']['userId'];
            $a = json_decode($this->line->getUserInfo($this->event['source']['userId']), true);
            $this->actor = $a['displayName'];
        }
    }

    private function parseReply()
    {
        if (isset($this->event['message']['text'])) {
            $this->reply = true;
            $wq = strtolower($this->event['message']['text']);
            switch ($wq) {
                case 'jadwal senin':
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage("Senin :\n\nUpacara\nB.Jawa\nB.Jawa\nAgama\nIstirahat\nB.Inggris\nB.Inggris\nFisika\nIstirahat\nFisika\nSeni Musik\nSeni Musik");
                    break;
                case 'jadwal selasa':
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage("Selasa :\n\nEkonomi\nEkonomi\nMat.Minat\nMat.Minat\nIstirahat\nKimia\nKimia\nB.Indo\nIstirahat\nB.Indo\nKWU\nKWU");
                    break;
                case 'jadwal rabu':
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage("Rabu :\n\nMat.wajib\nMat.wajib\nFisika\nFisika\nIstirahat\nBiologi\nBiologi\nEkonomi\nIstirahat\nEkonomi\nSejarah\nSejarah");
                    break;
                case 'jadwal kamis':
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage("Kamis :\n\nAgama\nAgama\nB.Indo\nB.Indo\nIstirahat\nPKN\nPKN\nBiologi\nIstirahat\nBiologi\nMat.Minat\nMat.Minat");
                    break;
                case 'jadwal jumat': case 'jadwal jum\'at':
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage("Jum'at :\n\nKimia\nKimia\nBK\nPenjas\nIstirahat\nPenjas\nPenjas\nIstirahat\nMat.wajib\nMat.wajib");
                    break;
                default:
                    $st = new AI();
                    $st->input($this->event['message']['text'], $this->actor);
                    if ($st->execute()) {
                        $reply = $st->output();
                        $reply = $reply['text'][0];
                        $this->line->buildMessage($this->room);
                        $this->line->textMessage($reply);
                    } else {
                        $this->reply = false;
                    }
                    break;
            }
        }
    }

    /**
     * Override.
     *
     * @param string $channel_token
     * @param string $channel_secret
     */
    public static function getInstance($channel_token, $channel_secret)
    {
        if (self::$instance === null) {
            self::$instance = new self($channel_token, $channel_secret);
        }
        return self::$instance;
    }
}

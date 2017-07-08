<?php

namespace Bot\Panel;

use IceTeaSystem\Hub\Singleton;

/**
 * @author Ammar Faizi <ammarfaizi2@gmail.com>
 * @package Bot\Telegram
 * @since 0.0.1
 */

class Telegram
{
    /**
     * Pakai singleton pattern.
     */
    use Singleton;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->keywords = file_exists(storage."/telegram/extended_keywords.json") ? json_decode(file_get_contents(storage."/telegram/extended_keywords.json"), true) : array();
        $this->keywords = is_array($this->keywords) ? $this->keywords : array();
    }

    /**
     * Run panel.
     */
    public static function run()
    {
        $self = self::getInstance();
        $self->show();
    }

    private function show()
    {
        include __DIR__."/panel_view.php";
    }
}

<?php

namespace Gurzhii\D2Parser\Parsers;

use Gurzhii\D2Parser\Html;

class BaseParser {

    const MATCH_STATUS_DEFAULT = self::MATCH_STATUS_FUTURE;

    const MATCH_STATUS_WIN_T1 = 1;
    const MATCH_STATUS_WIN_T2 = 2;
    const MATCH_STATUS_POSTPONED = 3;
    const MATCH_STATUS_PLAYING = 4;
    const MATCH_STATUS_FUTURE = 5;
    const MATCH_STATUS_CANCELED = 6;
    const MATCH_STATUS_DRAW = 7;
    const PARSER_SKIP = 8;
    const MATCH_POSTPONED = 'postponed';

    public static $progress_statuses = [
        self::MATCH_STATUS_PLAYING,
        self::MATCH_STATUS_FUTURE
    ];

    public $html;
    public $matches;
    public function __construct()
    {
        $html = Html::get(static::$url);
        if(!$html)throw new \Exception('no html data');
        $this->html = new \simple_html_dom($html);
    }

    public function getInitialStatus()
    {
        return self::MATCH_STATUS_DEFAULT;
    }
}
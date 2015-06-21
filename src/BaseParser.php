<?php

namespace Gurzhii\Parser;

class BaseParser {

    public $html;
    public function __construct()
    {
        $html = Html::get(static::$url);
        if(!$html)throw new \Exception('no html data');
        $this->html = new \simple_html_dom($html);
    }
}
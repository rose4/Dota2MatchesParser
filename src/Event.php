<?php

namespace Gurzhii\Parser;

class Event {
    public $title;

    public function __construct($data = [])
    {
        foreach($data as $k=>$v)
        {
            $this->$k = $v;
        }
    }

}
<?php

namespace Gurzhii\D2Parser;

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
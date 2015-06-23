<?php

namespace Gurzhii\D2Parser\Outputs;

class JsonOutput implements OutputInterface{

    public $data;

    function __construct($data)
    {
        $this->data = $data;
    }


    public function output()
    {
        header('Content-Type: application/json');
        return json_encode($this->data);
    }
}
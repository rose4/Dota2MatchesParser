<?php

namespace Gurzhii\D2Parser;

class Team {
    public $percent;
    public $picture;
    public $title;
    public $slug;

    public function __construct($data = [])
    {
        foreach($data as $k=>$v)
        {
            $this->$k = $v;
        }
        $this->slug = $this->setSlug();
    }

    public function setSlug()
    {
        return StringHelper::rus2str($this->title);
    }

}
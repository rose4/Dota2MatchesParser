<?php

namespace Parser;

class Match {
    public $event;
    public $team1;
    public $team2;
    public $full_url;
    public $original_id;
    public $start_time_stamp;
    public $stream_html;

    public function __construct(Event $event, Team $team1, Team $team2, $full_url, $original_id, $start_time_stamp)
    {
        $this->event = $event;
        $this->team1 = $team1;
        $this->team2 = $team2;
        $this->full_url = $full_url;
        $this->original_id = $original_id;
        $this->start_time_stamp = $start_time_stamp;
    }

}
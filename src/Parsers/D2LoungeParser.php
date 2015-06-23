<?php

namespace Gurzhii\D2Parser\Parsers;

use Gurzhii\D2Parser\Interfaces\ParserInterface;

use Gurzhii\D2Parser\StringHelper;
use Gurzhii\D2Parser\Event;
use Gurzhii\D2Parser\Html;
use Gurzhii\D2Parser\Match;
use Gurzhii\D2Parser\Team;

class D2LoungeParser extends BaseParser implements ParserInterface {

    public static $url = 'http://dota2lounge.com/';

    public function getStructuredDataSet()
    {
        $matchList = isset($this->html->find('#bets')[0]) ? $this->html->find('#bets')[0] : null;
        if($matchList)
        {
            $matchesList = $matchList->find('.matchmain');
            if(sizeof($matchesList))
            {
                foreach($matchesList as $matchEl)
                {
                    $absolute_url =  $this->getAbsoluteUrl($matchEl);
                    $title1 = $matchEl->find('.teamtext')[0]->find('b')[0]->plaintext;
                    $title2 = $matchEl->find('.teamtext')[1]->find('b')[0]->plaintext;
                    $match = new Match(
                        new Event(['title' => $matchEl->find('div.eventm')[0]->plaintext]),
                        new Team([
                            'title' => $title1,
                            'picture' => $this->getPicture($matchEl, 0),
                            'slug' => '',
                            'percent' => $this->getPercent($matchEl, 0)
                        ]),
                        new Team([
                            'title' => $title2,
                            'picture' => $this->getPicture($matchEl, 1),
                            'slug' => '',
                            'percent' => $this->getPercent($matchEl, 1)
                        ]),
                        $absolute_url,
                        $this->getOriginalId($absolute_url),
                        $this->getStartTimeStamp($matchEl, $absolute_url),
                        $this->getStatus($matchEl, $title1, $title2)
                    );
                    $this->matches[] = $match;
                }

            }
        }
        return $this->matches;
    }

    public function getStatus($matchEl, $team1_title, $team2_title)
    {
        if(
            strstr(strtolower($team1_title), 'tbd') ||
            strstr(strtolower($team2_title), 'tbd')
        )
        {
            $status = self::PARSER_SKIP;
            return $status;
        }
        $status = $this->getInitialStatus();

        $isPostponed = trim($matchEl->find('div.whenm span')[0]->plaintext);
        $start_time_text = $matchEl->find('div.whenm')[0]->innertext;

        $e = $matchEl->find('.match');
        if(strstr($e[0]->class, 'notavailable'))
        {
            if(!count($matchEl->find('.team')[0]->children()))
            {
                if(!count($matchEl->find('.team')[1]->children()))
                {
                    if(strstr($start_time_text, 'ago'))
                    {
                        if($isPostponed != self::MATCH_STATUS_POSTPONED)
                        {
                            $status = self::MATCH_STATUS_DRAW;
                        }
                    }
                }
            }
        }

        if(count($matchEl->find('.team')[0]->children()))
        {
            $status = self::MATCH_STATUS_WIN_T1;
        }
        if(count($matchEl->find('.team')[1]->children()))
        {
            $status = self::MATCH_STATUS_WIN_T2;
        }
        if(strstr($start_time_text, 'LIVE'))
        {
            $status = self::MATCH_STATUS_PLAYING;
        }
        if($isPostponed == self::MATCH_POSTPONED)
        {
            $status = self::MATCH_STATUS_POSTPONED;
        }
        else if(strstr($isPostponed, 'TBD'))
        {
            $status = self::PARSER_SKIP;
        }
        else if($isPostponed == 'wrong team')
        {
            $status = self::MATCH_STATUS_CANCELED;
        }
        else if($isPostponed == 'wrong match')
        {
            $status = self::MATCH_STATUS_CANCELED;
        }
        else if($isPostponed == 'forfeit')
        {
            $status = self::MATCH_STATUS_CANCELED;
        }
        else if($isPostponed == 'defwin')
        {
            $status = self::MATCH_STATUS_CANCELED;
        }
        return $status;
    }

    private function getStartTimeStamp($matchEl, $match_absolute_url = '')
    {
        $now_time = StringHelper::mk_time_cet();
        $startTimeNode_value = $matchEl->find('div.whenm')[0]->innertext;
        $startTimeString = trim(preg_replace('#<(' . implode( '|', ['span', 'b', 'em']) . ')(?:[^>]+)?>.*?</\1>#s', '', $startTimeNode_value));
        $diff = $this->convertTextTimeToS($startTimeString);
        if(strstr($startTimeNode_value, 'ago')){
            $start_time = $now_time - $diff;
        }else if(strstr($startTimeNode_value, 'from now')){
            $start_time = $now_time + $diff;
        }
        $date = date('d-m-Y', $start_time);
        $match_page = Html::get($match_absolute_url);
        if(!$match_page)return null;
        $match_start_time = $match_page->find('.box-shiny-alt')[0]->find('.half')[2]->plaintext;
        $f_date = trim($date . " " . $match_start_time);
        $stamp = StringHelper::mk_time_cet(true, $f_date);
        return $stamp;
    }

    private function convertTextTimeToS($textTime)
    {
        $seconds = 0;
        if(strstr($textTime, 'hours') || strstr($textTime, 'hour')){
            $h_total = filter_var($textTime, FILTER_SANITIZE_NUMBER_INT);
            $seconds = $h_total * (60 * 60);
        }else if(strstr($textTime, 'minute') || strstr($textTime, 'minutes')){
            $m_total = filter_var($textTime, FILTER_SANITIZE_NUMBER_INT);
            $seconds = $m_total * (60);
        }else if(strstr($textTime, 'day') || strstr($textTime, 'days')){
            $d_total = filter_var($textTime, FILTER_SANITIZE_NUMBER_INT);
            $seconds = (60 * 60 * 24) * $d_total;
        }
        return $seconds;
    }

    private function getOriginalId($absoluteUrl = '')
    {
        return explode('=', $absoluteUrl)[1];
    }

    private function getAbsoluteUrl($matchEl)
    {
        return static::$url . $matchEl->find('div.matchleft a')[0]->href;
    }

    private function getPercent($matchEl, $team_number = 0)
    {
        $string = $matchEl->find('.teamtext')[$team_number]->find('i')[0]->plaintext;
        return str_replace('%', '', $string);
    }


    private function getPicture($element = null, $team_number = 0)
    {
        $element->find('.team')[0]->style;
        preg_match("/(?:(?:\"(?:\\\\\"|[^\"])+\")|(?:'(?:\\\'|[^'])+'))/", $element->find('.team')[$team_number]->style, $m);
        return str_replace('\'', '', $m[0]);
    }

}
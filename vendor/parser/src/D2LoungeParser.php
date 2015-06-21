<?php

namespace Parser;

class D2LoungeParser extends BaseParser implements ParserInterface {

    public static $url = 'http://dota2lounge.com/';

    public function getStructuredDataSet()
    {
        $matches = [];
        $matchList = isset($this->html->find('#bets')[0]) ? $this->html->find('#bets')[0] : null;
        if($matchList)
        {
            $matchesList = $matchList->find('.matchmain');
            if(sizeof($matchesList))
            {
                foreach($matchesList as $matchEl)
                {
                    $absolute_url =  $this->getAbsoluteUrl($matchEl);
                    $match = new Match(
                        new Event(['title' => $matchEl->find('div.eventm')[0]->plaintext]),
                        new Team([
                            'title' => $matchEl->find('.teamtext')[0]->find('b')[0]->plaintext,
                            'picture' => $this->getPicture($matchEl, 0),
                            'slug' => '',
                            'percent' => $this->getPercent($matchEl, 0)
                        ]),
                        new Team([
                            'title' => $matchEl->find('.teamtext')[1]->find('b')[0]->plaintext,
                            'picture' => $this->getPicture($matchEl, 1),
                            'slug' => '',
                            'percent' => $this->getPercent($matchEl, 1)
                        ]),
                        $absolute_url,
                        $this->getOriginalId($absolute_url),
                        $this->getStartTimeStamp($matchEl, $absolute_url)
                    );
                    $matches[] = $match;
                }

            }
        }
        return $matches;
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
        return self::$url . $matchEl->find('div.matchleft a')[0]->href;
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
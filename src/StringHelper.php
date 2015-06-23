<?php

namespace Gurzhii\D2Parser;

class StringHelper {

    public static function mk_time_cet($stamp = true, $date = false, $format = 0){
        date_default_timezone_set("CET");
        $dt = (date("d-m-YTG:i:s", time()));
        if($date == false){
            $date = str_replace("CET", " ", $dt);
        }
        $d = strtotime($date);
        if($stamp)return $d;
        if($format == 0){
            return date('d-m-Y G:i:s', strtotime($date));
        }elseif($format == 1){
            return gmdate('Y-m-d H:i', $date);
        }
    }

    public static function timestamp_to_cest($t)
    {
        $year = strftime("%Y", $t);
        $initDay = (31 - ( floor(5 * $year / 4) + 4) % 7) ;
        $endDay = (31 - ( floor(5 * $year / 4) + 1) % 7) ;
        $initTime = strtotime("$initDay March $year");
        $endTime = strtotime("$initDay October $year");
        if ($t > $initTime && $t < $endTime) {
            return ($t + 3600);
        } else {
            return ($t);
        }
    }

    public static function rus2str($str=''){
        $trans = array(
            'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'yo', 'Ж' => 'zh', 'З' => 'z',
            'И' => 'i', 'Й' => 'i', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r',
            'С' => 's', 'Т' => 't', 'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'ts', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sch',
            'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            ' ' => '-', '!' => '', '?' => '', '('=> '', ')' => '', '#' => '', ',' => '', '№' => '',' - '=>'-','/'=>'-', '  '=>'-',
            'A' => 'a', 'B' => 'b', 'C' => 'c', 'D' => 'd', 'E' => 'e', 'F' => 'f', 'G' => 'g', 'H' => 'h', 'I' => 'i', 'J' => 'j', 'K' => 'k', 'L' => 'l', 'M' => 'm', 'N' => 'n',
            'O' => 'o', 'P' => 'p', 'Q' => 'q', 'R' => 'r', 'S' => 's', 'T' => 't', 'U' => 'u', 'V' => 'v', 'W' => 'w', 'X' => 'x', 'Y' => 'y', 'Z' => 'z'
        );
        return strtr($str, $trans);
    }
}
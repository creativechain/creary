<?php


namespace App\Utils;


class StringUtils
{

    /**
     * @param $regex
     * @param $str
     * @return array
     */
    public static function evalRegexp($regex, $str) {
        $matches = array();
        preg_match($regex, $str, $matches);
        return $matches[0];
    }
}

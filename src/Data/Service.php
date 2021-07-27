<?php


namespace App\Data;


use Cassandra\Date;

trait Service
{
    /**
     * @param string $text
     * @return string|string[]|null
     */
    public function slug_generate(string $text)
    {
        return preg_replace("/-$/","",preg_replace('/[^a-z0-9]+/i', "-", strtolower($text)));
    }

    /**
     * @param $date
     * @return string
     */
    public function time_ago($date)
    {
        $timestamp = strtotime($date);

        $strTime = array("second", "minute", "hour", "day", "month", "year");
        $length = array("60","60","24","30","12","10");

        $currentTime = time();
        if($currentTime >= $timestamp) {
            $diff     = time()- $timestamp;
            for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);
            return $diff . " " . $strTime[$i] . "(s) ago ";
        }
        //return $this->time_ago(new Date());
    }

}
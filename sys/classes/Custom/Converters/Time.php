<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Converters;

/**
 * Description of Time
 *
 * @author Tanzar
 */
class Time {
    
    public static function msToClock(int $time) : string {
        $hrs = floor($time / (60 * 60 * 1000));
        $mins = floor(($time - self::hrsToMs($hrs)) / (60 * 1000));
        $secs = floor(($time - self::hrsToMs($hrs) - self::minsToMs($mins)) / (60 * 1000));
        $text = '' . $hrs;
        if($mins < 10){
            $text .= ':0' . $mins;
        }
        else{
            $text .= ':' . $mins;
        }
        if($secs < 10){
            $text .= ':0' . $secs;
        }
        else{
            $text .= ':' . $secs;
        }
        return $text;
    }
    
    public static Function msToClockNoSeconds(int $time) : string {
        $hrs = floor($time / (60 * 60 * 1000));
        $mins = floor(($time - self::hrsToMs($hrs)) / (60 * 1000));
        $text = '' . $hrs;
        if($mins < 10){
            $text .= ':0' . $mins;
        }
        else{
            $text .= ':' . $mins;
        }
        return $text;
    }
    
    public static function msToFullHours(int $time) : string {
        $hrs = floor($time / (60 * 60 * 1000));
        $text = '' . $hrs;
        return $text;
    }
    
    private static function hrsToMs(int $hrs) : int {
        return $hrs * 60 * 60 *1000;
    }
    
    private static function minsToMs(int $mins) : int {
        return $mins * 60 * 1000;
    }
}

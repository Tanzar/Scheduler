<?php

namespace Tanweb;

/*
 * This code is free to use, just remember to give credit.
 */

/**
 * Class used to not call PHP functions directly, 
 * can help when versions of php are different and some functions dont work
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Utility {
    
    public static function isString($var){
        return is_string($var);
    }
    
    public static function isNotString($var){
        return !is_string($var);
    }
    
    public static function isNumeric($val){
        return is_numeric($val);
    }
    
    public static function stringContains($text, $searched){
        if(strpos($text, $searched)){
            return true;
        }
        return false;
    }
    
    public static function toLowerCase($text){
        return strtolower($text);
    }
    
    public static function getSubString($text, $startIndex, $endIndex){
        return substr($text, $startIndex, $endIndex);
    }
    
    public static function callFunction($method, $params){
        return call_user_func($method, $params);
    }
    
    public static function count($var){
        return count($var);
    }
    
    public static function toCharArray($str){
        if(self::isString($str)){
            return str_split($str);
        }
        return array();
    }
    
    public static function inArray($array, $searched){
        return in_array($searched, $array);
    }
    
    public static function areSameLength(...$arrays){
        $first = $arrays[0];
        $count = self::count($first);
        foreach ($arrays as $array){
            $countTmp = self::count($array);
            if($count !== $countTmp){
                return false;
            }
        }
        return true;
    }
    
    public static function toNumber($val){
        return intval($val);
    }
}

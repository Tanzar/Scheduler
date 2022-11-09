<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Converters;

/**
 * Description of Roman
 *
 * @author Tanzar
 */
class Roman {
    
    public static function toRoman(int $number) {
        $map = self::mapNumbers();
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
    
    public static function toInt(string $roman) : int {
        $romans = self::mapNumbers();
        $result = 0;

        foreach ($romans as $key => $value) {
            while (strpos($roman, $key) === 0) {
                $result += $value;
                $roman = substr($roman, strlen($key));
            }
        }
        return (int) $result;
    }
    
    private static function mapNumbers() : array {
        return array(
            'M' => 1000, 
            'CM' => 900, 
            'D' => 500, 
            'CD' => 400, 
            'C' => 100, 
            'XC' => 90, 
            'L' => 50, 
            'XL' => 40, 
            'X' => 10, 
            'IX' => 9, 
            'V' => 5, 
            'IV' => 4, 
            'I' => 1
        );
    }
}

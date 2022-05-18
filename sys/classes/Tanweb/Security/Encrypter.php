<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Security;

/**
 * Class for managing encryption of passwords
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Encrypter {
    
    public static function encode(string $text) : string {
        return password_hash($text, PASSWORD_BCRYPT);
    }
    
    /**
     * COmpares if two strings are the same
     * 
     * @param string $encoded - result of encode() method (mostly stored passwords)
     * @param string $uncoded - uncoded text
     * @return bool true if are same, false if not
     */
    public static function areSame(string $encoded, string $uncoded) : bool {
        return password_verify($uncoded, $encoded);
    }
}

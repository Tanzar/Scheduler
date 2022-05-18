<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb;

/**
 * Class to manage session, get some informations from requests
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Session {
    
    public static function getUsername() : string {
        if(isset($_SESSION['username'])){
            return $_SESSION['username'];
        }
        else{
            return '';
        }
    }
    
    public static function getIP() : string {
        $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        if($ip){
            return $ip;
        }
        else{
            return 'undefined';
        }
    }
    
    public static function getProxy() : string {
        $proxy = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        if($proxy){
            return $proxy;
        }
        else{
            return 'undefined';
        }
    }
    
    public static function setUser(string $username) : void {
        $_SESSION['username'] = $username;
    }
    
    public static function unsetUser() : void {
        unset($_SESSION['username']);
    }
}

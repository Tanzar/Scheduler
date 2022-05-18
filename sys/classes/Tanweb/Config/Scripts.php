<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\Server as Server;

/**
 * Class to manage php scripts files links,
 * can be used to run script files, 
 * should rarely be used
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Scripts{
    private static $dir = 'sys/scripts';
    
    /**
     * find link to file
     * 
     * @param string $filename name of script file
     * @return string link to file
     */
    public static function get(string $filename) : string{
        $files = Server::getFilesPaths(self::$dir);
        $path = $files->getValue($filename);
        $url = Server::getRootURL();
        $url .= $path;
        return $url;
    }
    
    /**
     * finds all script files
     * 
     * @return array links to script files
     */
    public static function getAll() : array{
        $files = Server::getFilesPaths(self::$dir);
        $url = Server::getRootURL();
        $result = array();
        $filesArray = $files->toArray();
        foreach ($filesArray as $key => $file){
            $result[$key] = $url . $file;
        }
        return $result;
    }
    
    /**
     * "Runs" script file by using require
     * 
     * @param string $filename name of script file
     * @return void
     */
    public static function run(string $filename) : void{
        $files = Server::getFilesPaths(self::$dir);
        $file = $files->getValue($filename);
        $path = Server::getLocalRoot() . $file;
        require $path;
    }
}

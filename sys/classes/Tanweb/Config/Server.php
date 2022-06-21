<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;

/**
 * Class used to find files, paths, links
 * Helps manage most of config
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Server {
    
    /**
     * Method gets link to index page
     * 
     * @return string link to index page
     */
    public static function getIndexPath() {
        $path = self::getRootURL() . 'index.php';
        return $path;
    }
    
    /**
     * Gets path to root in local filesystem
     * 
     * @return string path to project root in filesystem
     */
    public static function getLocalRoot(): string{
        $appConfig = AppConfig::getInstance();
        $config = $appConfig->getAppConfig();
        if($config->isValueSet('name')){
            $projectName = $config->get('name');
            $path = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . $projectName . '/';
        }
        else{
            $path = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/';
        }
        return $path;
    }
    
    /**
     * generates url link to project
     * not tested on server with access from outside local network
     * 
     * @return type
     */
    public static function getRootURL() : string{
        $appConfig = AppConfig::getInstance();
        $config = $appConfig->getAppConfig();
        if($config->isValueSet('name')){
            $appName = $config->get('name');
            return self::createServerURL($appName);
        }
        else{
            return self::createServerURL();
        }
    }
    
    private static function createServerURL(string $appName = null) : string{
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
        }
        else{
            $url = "http://";
        }
        $url .= $_SERVER['HTTP_HOST'] . '/';
        if(isset($appName)){
            $url .= $appName . '/';
        }
        return $url;
    }
    
    /**
     * finds all files in specified directory
     * 
     * @param string $dir directory to search, path from local root
     * @return Container container with paths to files, filenames are indexes
     */
    public static function getFilesPaths(string $dir) : Container{
        $localRoot = self::getLocalRoot();
        $path = $localRoot . $dir;
        $paths = self::listFiles($path);
        $results = new Container();
        foreach($paths as $item){
            $name = basename($item);
            $localPath = self::cutPath($item, $localRoot);
            $results->add($localPath, $name);
        }
        return $results;
    }
    
    private static function listFiles(string $dir) : array{
        $root = scandir($dir);
        $result = array();
        foreach($root as $value)
        {
            if($value === '.' || $value === '..') {continue;}
            if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;}
            foreach(self::listFiles("$dir/$value") as $value)
            {
                $result[]=$value;
            }
        }
        return $result;
    }
    
    private static function cutPath(string $path, string $toCut) : string{
        return str_replace($toCut, '', $path);
    }
    
    /**
     * gets full request link (URL)
     * 
     * @return string link
     */
    public static function getRequestUrl() : string{
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
        }
        else{
            $url = "http://";
        }
        $url .= filter_input(INPUT_SERVER, 'HTTP_HOST');
        $url .= filter_input(INPUT_SERVER, 'REQUEST_URI');
        return $url;
    }
    
    public static function getRefferUrl(){
        return filter_input(INPUT_SERVER, 'HTTP_REFERER');
    }
}

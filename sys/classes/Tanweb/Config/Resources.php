<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\Server as Server;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;

/**
 * Class to manage resources in app, 
 * methods find files by themselves, no need to configure it
 * use it on pages to link files like css, js, files to download, external resources
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Resources {
    
    /**
     * finds file in css directory
     * 
     * @param string $filename name of css file (with extension like 'main.css')
     * @return string link to file
     */
    public static function getCSS(string $filename) : string{
        $dir = 'resources/css';
        return self::getFileURL($dir, $filename);
    }
    
    /**
     * adds css file (as htmlelement) to page from css directory
     * 
     * @param string $filemane name of css file (with extension like 'main.css')
     * @return void
     */
    public static function linkCSS(string $filemane) : void{
        $link = self::getCSS($filemane);
        $html = '<link rel="stylesheet" href="' . $link . '">';
        echo $html;
    }
    
    /**
     * finds file in downloads directory
     * 
     * @param string $filename name of file to find, add extension
     * @return string link to file
     */
    public static function getDownload(string $filename) : string{
        $dir = 'resources/download';
        return self::getFileURL($dir, $filename);
    }
    
    /**
     * adds link to file form downloads directory
     * 
     * @param string $filename nameof file, use extension
     * @param string $innerHtml htmlelement to whitch link will be added, can be simple text, can be image
     * @param bool $download changes behavior of link if true it will instantly download file, if false it will just link to file (in most cases redirect), by default is true
     */
    public static function linkDownload(string $filename, string $innerHtml, bool $download = true){
        $link = self::getDownload($filename);
        $html = '<a href="' . $link . '"';
        if($download) {
            $html .= 'download>';
        }
        else{
            $html .= '>';
        }
        $html .= $innerHtml . '</a>';
        echo $html;
    }
    
    /**
     * finds files in img directory
     * 
     * @param string $filename name of file, use extensions
     * @return string link to file
     */
    public static function getIMG(string $filename) : string{
        $dir = 'resources/img';
        return self::getFileURL($dir, $filename);
    }
    
    /**
     * adds image (as htmlelement) to page form img directory
     * 
     * @param string $filename name of file, use extensions
     * @param string $id html id if needed
     * @return void
     */
    public static function linkIMG(string $filename, string $id = null) : void{
        $link = self::getIMG($filename);
        $html = '<img ';
        if(isset($id)){
            $html .= 'id="' . $id . '"';
        }
        $html .= ' src="' . $link . '">';
        echo $html;
    }
    
    /**
     * finds files in js directory
     * 
     * @param string $filename name of file, use extensions
     * @return string link to file
     */
    public static function getJS(string $filename) : string{
        $dir = 'resources/js';
        return self::getFileURL($dir, $filename);
    }
    
    /**
     * add javascript file (using html) to page form js directory
     * 
     * @param type $filename name of file, use extensions
     * @return void
     */
    public static function linkJS($filename) : void{
        $link = self::getJS($filename);
        $html = '<script type="text/javascript" id="' . $filename . '" src="' . $link . '"></script>';
        echo $html;
    }
    
    /**
     * adds external resource to page (like external stript libraries eg. jquery)
     * needs to be configured in config.ini
     * 
     * @param string $index index from config.ini file, section external_resources
     */
    public static function linkExternal(string $index){
        $appconfig = AppConfig::getInstance();
        $config = $appconfig->getExternalResources();
        $ext = new Container($config->get($index));
        $link = $ext->get('link');
        if(self::isNotInternetAccess() && $ext->isValueSet('local')){
            $link  = self::localLinkToResources($ext);
        }
        switch ($ext->get('type')){
            case 'js':
                echo '<script src="' . $link .'" type="text/javascript"></script>';
                break;
            case 'css':
                echo '<script type="text/javascript" src="' . $link . '"></script>';
                break;
            case 'link':
                echo '<a href="' . $link . '">' . $index . '</a>';
                break;
            default :
                echo 'Error: resource handling not defined for - ' . $ext->get('type');
        }
    }
    
    //not 100% sure if it works, I didn't have a chance to test it
    private static function isNotInternetAccess() : bool{
        $connected = @fsockopen("www.google.com", 80); 
        if ($connected){
            $isConnected = false;
            fclose($connected);
        }else{
            $isConnected = true;
        }
        return $isConnected;
    }
    
    private static function localLinkToResources(Container $ext) : string {
        $type = $ext->get('type');
        switch($type){
            case 'js':
                return self::getJS($ext->get('local'));
            case 'css':
                return self::getCSS($ext->get('local'));
            default:
                echo 'Error: resource handling not defined for - ' . $ext->get('type');
        }
    }
    
    /**
     * locates files in directory and subdirectories
     * 
     * @param string $dir directory to search
     * @param string $filename file to find, use extensions
     * @return string link to file
     */
    private static function getFileURL(string $dir, string $filename) : string{
        $files = Server::getFilesPaths($dir);
        $path = $files->get($filename);
        $url = Server::getRootURL();
        $url .= $path;
        return $url;
    }
}
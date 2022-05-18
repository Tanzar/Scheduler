<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\Server as Server;

/**
 * Class to manage pages in pages directory
 * should only contain php and html files
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Pages{
    private static $dir = 'pages';
    
    /**
     * finds page link (address)
     * 
     * @param string $pageFile name of file, use extensions
     * @return string link to page
     */
    public static function getURL(string $pageFile) : string{
        $files = Server::getFilesPaths(self::$dir);
        $path = $files->getValue($pageFile);
        $url = Server::getRootURL();
        $url .= $path;
        return $url;
    }
}

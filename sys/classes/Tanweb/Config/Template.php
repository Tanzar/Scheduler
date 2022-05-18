<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\Server as Server;

/**
 * Class to manage template directory,
 * should contain files used as templates for project
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Template{
    private static $dir = 'sys/templates';
    
    /**
     * Gets local path to template file
     * 
     * @param string $templateName name of template file, use extensions
     * @return string local path to file
     */
    public static function getLocalPath(string $templateName) : string{
        $files = Server::getFilesPaths(self::$dir);
        $path = $files->getValue($templateName);
        $localPath = Server::getLocalRoot();
        $localPath .= $path;
        return $localPath;
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\Config\Server as Server;
use Tanweb\Config\TemplateException as TemplateException;
use Tanweb\Container as Container;

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
        $path = $files->get($templateName);
        $localPath = Server::getLocalRoot();
        $localPath .= $path;
        return $localPath;
    }
    
    /**
     * Gets local path to template file in directory inside templates
     * 
     * @param string $templateName name of template file, use extensions
     * @return string local path to file
     */
    public static function getLocalPathInnerDir(string $templateName) : string{
        $files = Server::getFilesPaths(self::$dir);
        $path = $files->get($templateName);
        $localPath = Server::getLocalRoot();
        $localPath .= $path;
        return $localPath;
    }
    
    /**
     * Lists template files inside given directory
     * 
     * @param string $dir directory inside templates, by default it listst all templates
     * @return Container containing names of files
     */
    public static function listTemplates(string $dir = '') : Container {
        $path = self::$dir;
        if($dir !== ''){
            $path .= '/' . $dir;
        }
        $files = Server::getFilesPaths($path);
        $result = new Container();
        foreach ($files->toArray() as $filename => $path) {
            $result->add($filename);
        }
        return $result;
    }
    
    /**
     * Uploads file to template directory
     * 
     * @param Container $file file to upload
     * @param string $dir directory inside template to put file in
     * @return void
     * @throws TemplateException
     */
    public static function uploadFile(Container $file, string $dir = '') : void {
        if($file->get('error') > 0){
            throw new TemplateException($file->get('error'));
        }
        else{
            $path = Server::getLocalRoot() . '/' . self::$dir;
            if($dir !== ''){
                $path .= '/' . $dir;
            }
            move_uploaded_file($file->get('tmp_name'), $path . '/' . $file->get('name'));
        }
    }
}

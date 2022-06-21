<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config\INI;

use Tanweb\Container as Container;
use Tanweb\Config\ConfigException as ConfigException;
use Tanweb\Session as Session;
use Tanweb\Utility as Utility;

/**
 * Description of Languages
 *
 * @author Tanzar
 */
class Languages {
    private static Languages $instance;
    private Container $messages;
    
    protected function __construct(string $language) {
        $projectName = explode('/', filter_input(INPUT_SERVER, 'REQUEST_URI'))[1];
        $path =  filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . $projectName . '/';
        $path = $path . 'config/languages.ini';
        $ini = parse_ini_file($path, true);
        if(isset($ini[$language])){
            $this->messages = new Container($ini[$language]);
        }
        else{
            throw new ConfigException('language ' . $language . ' not defined in languages.ini file.');
        }
    }
    
    public static function getInstance(string $language = null) : Languages {
        if(isset(self::$instance)){
            return self::$instance;
        }
        else{
            $language = Session::getLanguageSettings();
            self::$instance = new Languages($language);
            return self::$instance;
        }
    }
    
    public static function getLanguageOptions() : array{
        $projectName = explode('/', filter_input(INPUT_SERVER, 'REQUEST_URI'))[1];
        $path =  filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . $projectName . '/';
        $path = $path . 'config/languages.ini';
        $ini = parse_ini_file($path, true);
        $options = Utility::getKeys($ini);
        return $options;
    }
    
    public function get(string $index){
        if($this->messages->isValueSet($index)){
            return $this->messages->get($index);
        }
        else{
            throw new ConfigException('message ' . $index . ' not defined in languages.ini file.');
        }
    }
    
    public function getPackage() : Container {
        return $this->messages;
    }
}

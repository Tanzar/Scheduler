<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config\INI;

use Tanweb\Container as Container;
use Tanweb\Config\ConfigException as ConfigException;

/**
 * Class to manage config.ini file, allows to read properities
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class AppConfig {
    private static AppConfig $instance;
    private Container $app;
    private Container $databases;
    private Container $security;
    private Container $mailer;
    private Container $logger;
    private Container $externalResources;
    
    protected function __construct() {
        $projectName = explode('/', filter_input(INPUT_SERVER, 'REQUEST_URI'))[1];
        $path =  filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/' . $projectName . '/';
        $path = $path . 'config/config.ini';
        $ini = parse_ini_file($path, true);
        if(isset($ini['app'])){
            $this->app = new Container($ini['app']);
        }
        if(isset($ini['databases'])){
            $this->databases = new Container($ini['databases']);
        }
        if(isset($ini['security'])){
            $this->security = new Container($ini['security']);
        }
        if(isset($ini['mailer'])){
            $this->mailer = new Container($ini['mailer']);
        }
        if(isset($ini['logger'])){
            $this->logger = new Container($ini['logger']);
        }
        if(isset($ini['external_resources'])){
            $this->externalResources = new Container($ini['external_resources']);
        }
    }
    
    public static function getInstance() : AppConfig {
        if(isset(self::$instance)){
            return self::$instance;
        }
        else{
            self::$instance = new AppConfig();
            return self::$instance;
        }
    }
    
    public function getAppConfig() : Container{
        if(isset($this->app)){
            return $this->app;
        }
        else{
            $this->throwException('app not defined.');
        }
    }
    
    public function getDatabase(string $index) : Container{
        if(isset($this->databases)){
            $database = $this->databases->getValue($index);
            return new Container($database);
        }
        else{
            $this->throwException('database not defined for index: ' . $index . '.');
        }
    }
    
    public function getSecurity() : Container{
        if(isset($this->security)){
            return $this->security;
        }
        else{
            $this->throwException('security not defined.');
        }
    }
    
    public function getMailer() : Container {
        if(isset($this->mailer)){
            return $this->mailer;
        }
        else{
            $this->throwException('mailer not defined.');
        }
    }
    
    public function getLogger() : Container {
        if(isset($this->logger)){
            return $this->logger;
        }
        else{
            $this->throwException('logger not defined.');
        }
    }
    
    public function getExternalResources() : Container {
        if(isset($this->externalResources)){
            return $this->externalResources;
        }
        else{
            $this->throwException('external_resources not defined.');
        }
    }
    
    private function throwException($msg){
        throw new ConfigException('AppConfig error: ' . $msg);
    }
}
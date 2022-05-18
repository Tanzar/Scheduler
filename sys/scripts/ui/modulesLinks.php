<?php

/* 
 * This code is free to use, just remember to give credit.
 */
$projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
use Tanweb\Container as Container;
use Tanweb\Config\Server as Server;
use Tanweb\Config\Pages as Pages;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Security\Security as Security;

try{
    $appconfig = new AppConfig();
    $config = $appconfig->getAppConfig();
    $modules = new Container($config->getValue('modules'));
    $security = new Security();
    
    //--- INDEX ---//
    echo '<form action="' . Server::getIndexPath() . '">';
    echo '<input type="submit" value="Index">';
    echo '</form>';
    
    //--- ADMIN PANEL ---//
    if($security->userHaveAnyPrivilage(new Container(['admin']))){
        echo '<form action="' . Pages::getURL('adminPanel.php') . '">';
        echo '<input type="submit" value="' . $modules->getValue('admin') . '">';
        echo '</form>';
    }
    
    //--- SCHEDULE ---//
    if($security->userHaveAnyPrivilage(new Container(['admin', 'schedule_user', 'schedule_admin']))){
        echo '<form action="' . Pages::getURL('schedule.php') . '">';
        echo '<input type="submit" value="' . $modules->getValue('schedule') . '">';
        echo '</form>';
    }
    
    //--- INSPECTOR ---//
    //TODO
    
    //--- ARCHIVIST ---//
    //TODO
    
    //--- STATISTICS ---//
    //TODO
    
    //--- HELPDESK ---//
    //TODO
    
}
catch (Throwable $ex){
    
}
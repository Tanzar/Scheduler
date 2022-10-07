<?php

/* 
 * This code is free to use, just remember to give credit.
 */
$projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
use Tanweb\Container as Container;
use Tanweb\Config\Server as Server;
use Tanweb\Config\Pages as Pages;
use Tanweb\Security\Security as Security;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Session as Session;

try{
    $languages = Languages::getInstance();
    $modules = new Container($languages->get('modules'));
    $security = Security::getInstance();
    
    //--- INDEX ---//
    echo '<form action="' . Server::getIndexPath() . '">';
    echo '<input type="submit" class="standard-button" value="Index">';
    echo '</form>';
    
    //--- ADMIN PANEL ---//
    if($security->userHaveAnyPrivilage(new Container(['admin']))){
        echo '<form action="' . Pages::getURL('adminPanelUsers.php') . '">';
        echo '<input type="submit" class="standard-button" value="' . $modules->get('admin') . '">';
        echo '</form>';
    }
    
    //--- SCHEDULE ---//
    if($security->userHaveAnyPrivilage(new Container(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']))){
        echo '<form action="' . Pages::getURL('scheduleAll.php') . '">';
        echo '<input type="submit" class="standard-button" value="' . $modules->get('schedule') . '">';
        echo '</form>';
    }
    
    //--- INSPECTOR ---//
    if($security->userHaveAnyPrivilage(new Container(['admin', 'schedule_user_inspector']))){
        echo '<form action="' . Pages::getURL('inspectorReports.php') . '">';
        echo '<input type="submit" class="standard-button" value="' . $modules->get('inspector') . '">';
        echo '</form>';
    }
    
    //--- ARCHIVIST ---//
    //TODO
    
    //--- STATISTICS ---//
    //TODO
    
    //--- FILES ---//
    //TODO
    
}
catch (Throwable $ex){
    
}
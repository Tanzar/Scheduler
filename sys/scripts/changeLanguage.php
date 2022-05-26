<?php

/* 
 * This code is free to use, just remember to give credit.
 */
session_start();
$projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';

use Tanweb\Request\Request as Request;
use Tanweb\Config\Server as Server;
use Tanweb\Session as Session;

try{
    $request = new Request(false);
    $language = $request->get('language');
    Session::setLanguage($language);
    echo '<script>';
    $path = Server::getIndexPath();
    echo 'window.location.replace("' . $path . '")';
    echo '</script>';
}
catch (Throwable $ex){
    $msg = $ex->getMessage();
    $trace = $ex->getTraceAsString();
    echo '<script>';
    $path = Server::getIndexPath();
    echo 'console.log("' . $msg . '");';
    echo 'alert("' . $msg . '");';
    echo 'window.location.replace("' . $path . '")';
    echo '</script>';
}
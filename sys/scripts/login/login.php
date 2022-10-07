<?php

/* 
 * This code is free to use, just remember to give credit.
 */

session_start();
$projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';

use Tanweb\Request\Request as Request;
use Tanweb\Config\Server as Server;
use Tanweb\Security\Security as Security;


echo '<script>';

try{
    $request = new Request(false);
    $username = $request->get('username');
    $security = Security::getInstance();
    
    if($security->isUsingPasswords()){
        $password = $request->get('password');
        $security->login($username, $password);
    }
    else{
        $security->login($username);
    }
}
catch (Throwable $ex){
    $msg = $ex->getMessage();
    $trace = $ex->getTraceAsString();
    echo 'console.log("' . $msg . '");';
    echo 'alert("' . $msg . '");';
}

$path = Server::getIndexPath();
echo 'window.location.replace("' . $path . '")';
echo '</script>';
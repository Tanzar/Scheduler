<?php

/* 
 * This code is free to use, just remember to give credit.
 */


session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Tanweb\Request\Request as Request;
use Tanweb\Config\Server as Server;
use Tanweb\Security\Security as Security;


echo '<script>';

try{
    $security = Security::getInstance();
    $security->logout();
}
catch (Exception $ex){
    $msg = $ex->getMessage();
    echo 'console.log("' . $msg . '");';
    echo 'alert("' . $msg . '");';
}

$path = Server::getIndexPath();
echo 'window.location.replace("' . $path . '")';
echo '</script>';
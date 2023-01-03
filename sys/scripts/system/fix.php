<?php

/* 
 * This code is free to use, just remember to give credit.
 */
error_reporting(E_ERROR | E_PARSE);

session_start();
if($_SERVER['DOCUMENT_ROOT'] !== ''){
    $autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
}
else{
    $autoloadPath = '/var/www/html/vendor/autoload.php';
}
require_once $autoloadPath;

use Custom\DBFixer\DBFixer as DBFixer;
use Tanweb\Security\Security as Security;
use Tanweb\Database\Database as Database;

if($_SERVER['DOCUMENT_ROOT'] !== ''){
    $security = Security::getInstance();
    $security->userHavePrivilage('admin');
}
try{
    $text = DBFixer::run();
    Database::finalizeAll();
    echo $text;
}
catch(Throwable $th){
    Database::rollbackAll();
}
?>
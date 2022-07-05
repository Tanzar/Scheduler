<?php

/* 
 * This code is free to use, just remember to give credit.
 */
$projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';

use Tanweb\Session as Session;

?>
<div class="standard-text">
    <?php 
        echo 'Witaj ' . Session::getUsername();
    ?>
</div>
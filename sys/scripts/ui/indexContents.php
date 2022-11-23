<?php

/* 
 * This code is free to use, just remember to give credit.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Tanweb\Session as Session;

?>
<div class="standard-text">
    <?php 
        echo 'Witaj ' . Session::getUsername();
    ?>
</div>
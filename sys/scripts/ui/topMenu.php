<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Session as Session;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
?>

<div class="top-menu">
    <div class="top-menu-left">
        <?php
            Resources::linkIMG('logo-alt.jpg', 'logo_img');
            Scripts::run('modulesLinks.php');
        ?>
    </div>
    <div class="top-menu-right">
        <?php
            echo Session::getUsername() . '<br>';
        ?>
        <form action="<?php echo Scripts::get('logout.php'); ?>">
            <input type="submit" value="Wyloguj">
        </form>
    </div>
</div>
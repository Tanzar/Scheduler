<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Session as Session;
?>
<!DOCTYPE html>
<!--
This code is free to use, just remember to give credit.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>
            <?php
                $appconfig = AppConfig::getInstance();
                $cfg = $appconfig->getAppConfig();
                echo $cfg->getValue('name');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkJS('RestApi.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <div class="side-menu">
            <button id="my_data">Moje Dane</button>
        </div>
        <div class="page-contents">
            <?php 
                $username = Session::getUsername();
                if($username === ''){
                    Scripts::run('loginForm.php');
                }
                else{
                    echo 'Witaj ' . $username;
                }
            ?>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
    </script>
</html>

<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Session as Session;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    use Tanweb\Security\PageAccess as PageAccess;
    
    PageAccess::blockInternetExplorer();
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
?>
<!DOCTYPE html>
<!--
This code is free to use, just remember to give credit.
-->
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF8">
        <title>
            <?php
                $appconfig = AppConfig::getInstance();
                $cfg = $appconfig->getAppConfig();
                echo $cfg->get('name');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('index.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php
            Scripts::run('indexSideMenu.php');
        ?>
        <div class="page-contents-centered">
            <?php 
                $username = Session::getUsername();
                if($username === ''){
                    Scripts::run('loginForm.php');
                }
                else{
                    Scripts::run('indexContents.php');
                }
            ?>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        <?php
            if($username !== ''){
                echo 'init();';
            }
        ?>
    </script>
</html>

<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
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
        <meta charset="UTF-8">
        <title>
            <?php
                $appconfig = AppConfig::getInstance();
                $cfg = $appconfig->getAppConfig();
                echo $cfg->get('name');
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('inspector');
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
            <?php
                Scripts::run('inspectorSideMenu.php');
            ?>
        </div>
        <div class="page-contents-centered">
            
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        RestApi.getInterfaceNamesPackage(function(package){
            console.log(package);
        });
        
    </script>
</html>

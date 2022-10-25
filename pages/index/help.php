<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    use Tanweb\Security\Security as Security;
    
    PageAccess::allowFor([]);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $security = Security::getInstance();
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
        <?php
            Scripts::run('indexSideMenu.php');
        ?>
        <div class="page-contents">
            <div class="standard-text">
                <?php 
                    Resources::linkDownload('user_manual.pdf', $interface->get('user_manual'), false);
                ?>
            </div>
            <?php 
                if($security->userHaveAnyPrivilage(new Container(['admin']))){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('admin_manual.pdf', $interface->get('admin_manual'), false);
                    echo '</div>';
                }
            ?>
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

<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Container as Container;
    
    PageAccess::allowFor(['admin']);   //locks access if failed to pass redirects to index page
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
                $appconfig = new AppConfig();
                $cfg = $appconfig->getAppConfig();
                echo $cfg->getValue('name') . ': ';
                $modules = new Container($cfg->getValue('modules'));
                echo ' : ' . $modules->getValue('admin');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('tab-menu.css');
            Resources::linkCSS('datatable.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('TabMenu.js');
            Resources::linkJS('adminPanel.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <div class="side-menu">
            <button id="users">Użytkownicy</button>
            <button id="schedule"><?php echo $modules->getValue('schedule'); ?></button>
        </div>
        <div class="page-contents" id="display"></div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        AdminPanel();
    </script>
</html>

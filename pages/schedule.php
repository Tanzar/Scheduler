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
    
    PageAccess::allowFor(['admin', 'schedule_user', 'schedule_admin']);   //locks access if failed to pass redirects to index page
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
                $modules = new Container($cfg->getValue('modules'));
                echo ' : ' . $modules->getValue('schedule');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('timetable.css');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('Timetable.js');
            Resources::linkJS('schedule.js');
            Resources::linkJS('scheduleMy.js');
            Resources::linkJS('scheduleAll.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <div class="side-menu">
            <button id="all">Wszyscy</button>
            <button id="my_entries">Moje Wpisy</button>
        </div>
        <div class="page-contents">
            <div>
                <input type="date" value="<?php echo date('Y-m-d'); ?>" >
                <button id="selectDate">Wybierz</button>
            </div>
            <div id="timetable" class="timetable"></div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        init();
    </script>
</html>

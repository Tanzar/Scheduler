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
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    
    PageAccess::allowFor(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);   //locks access if failed to pass redirects to index page
    
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
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('schedule');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('timetable.css');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('Timetable.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('scheduleAll.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('scheduleSideMenu.php') ?>
        <div class="page-contents" id="pageContents">
            <div class="centered-contents">
                <div>
                    <label class="standard-text">
                        <?php echo $interface->get('select_date'); ?>
                    </label>
                    <input id="displayDate" type="date" class="standard-input"
                           value="<?php echo date('Y-m-d'); ?>">
                    <button id="goTo" class="standard-button">
                        <?php echo $interface->get('go_to'); ?>
                    </button>
                </div>
                <div id="timetable"></div>
            </div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        scheduleAll();
    </script>
</html>

<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    
    PageAccess::allowFor(['admin', 'stats_user', 'stats_admin']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $interfaceText = new Container($languages->get('interface'));
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
                echo $cfg->get('name') . ': ';
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('admin');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('datatable.css');
            Resources::linkCSS('modal-box.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('modalBox.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('statsSanctions.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('statsSideMenu.php'); ?>
        <div class="page-contents-centered" id="display">
            <div class="horizontal-container">
                <div class="standard-text"><?php echo $interfaceText->get('select_sanction_type') . ':'; ?></div>
                <select id="sanctionType" class="standard-input">
                    <option value="tickets"><?php echo $interfaceText->get('tickets'); ?></option>
                    <option value="articles"><?php echo $interfaceText->get('art_41'); ?></option>
                    <option value="decisions"><?php echo $interfaceText->get('decisions'); ?></option>
                    <option value="suspensions"><?php echo $interfaceText->get('suspensions'); ?></option>
                    <option value="usages"><?php echo $interfaceText->get('instrument_usages'); ?></option>
                    <option value="court"><?php echo $interfaceText->get('court_applications'); ?></option>
                </select>
            </div>
            <div>
                <?php 
                    Scripts::run('selectMonthYear.php');
                ?>
            </div>
            <div id="datatable"></div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        init();
    </script>
</html>

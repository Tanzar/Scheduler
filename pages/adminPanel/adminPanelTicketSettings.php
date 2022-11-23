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
    
    PageAccess::allowFor(['admin']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $adminOptions = new Container($languages->get('admin'));
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
            Resources::linkJS('adminPanelTicketSettings.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('adminPanelSideMenu.php'); ?>
        <div class="page-contents" id="display">
            <div class="horizontal-container">
                <div>
                    <div class="standard-text-title">
                        <?php echo $interfaceText->get('ticket_laws'); ?>
                    </div>
                    <div id="ticketLaws"></div>
                </div>
                <div>
                    <div class="standard-text-title">
                        <?php echo $interfaceText->get('position_groups'); ?>
                    </div>
                    <div id="positionGroups"></div>
                </div>
            </div>
            
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        init();
    </script>
</html>

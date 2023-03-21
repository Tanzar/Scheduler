<?php 
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
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
            <?php 
                echo '<div class="standard-text">';
                Resources::linkDownload('user_manual.pdf', $interface->get('user_manual'), false, true);
                echo '</div>';
                
                if($security->userHaveAnyPrivilage(new Container(['admin']))){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('admin_manual.pdf', $interface->get('admin_manual'), false, true);
                    echo '</div>';
                }
                
                $schedulePrivilages = new Container(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);
                if($security->userHaveAnyPrivilage($schedulePrivilages)){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('user_manual_schedule.pdf', $interface->get('schedule_manual'), false, true);
                    echo '</div>';
                }
                
                $inspectorPrivilages = new Container(['admin', 'schedule_user_inspector']);
                if($security->userHaveAnyPrivilage($inspectorPrivilages)){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('user_manual_inspector.pdf', $interface->get('inspector_manual'), false, true);
                    echo '</div>';
                }
                
                $inventoryPrivilages = new Container(['admin', 'inventory_user', 'inventory_admin']);
                if($security->userHaveAnyPrivilage($inventoryPrivilages)){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('user_manual_inventory.pdf', $interface->get('inventory_manual'), false, true);
                    echo '</div>';
                }
                
                $quaificationsPrivilages = new Container(['admin', 'qualification_user']);
                if($security->userHaveAnyPrivilage($quaificationsPrivilages)){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('user_manual_qualifications.pdf', $interface->get('qualifications_manual'), false, true);
                    echo '</div>';
                }
                
                $statsPrivilages = new Container(['admin', 'stats_user', 'stats_admin']);
                if($security->userHaveAnyPrivilage($statsPrivilages)){
                    echo '<div class="standard-text">';
                    Resources::linkDownload('user_manual_stats.pdf', $interface->get('stats_manual'), false, true);
                    echo '</div>';
                }
            ?>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
</html>

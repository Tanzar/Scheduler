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
    use Services\LocationService as LocationService;
    
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
            Resources::linkCSS('tab-menu.css');
            Resources::linkCSS('datatable.css');
            Resources::linkCSS('modal-box.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('modalBox.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('TabMenu.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('adminPanelLocations.js');
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
                        <?php echo $interfaceText->get('location'); ?>
                    </div>
                    <div class="horizontal-container">
                        <div class="standard-text">
                            <?php echo $interfaceText->get('select_location_group') ?>
                        </div>
                        <select class="standard-input" id="selectLocationType">
                            <?php
                                $service = new LocationService();
                                $types = $service->getAllLocationGroups();
                                foreach ($types->toArray() as $item){
                                    $type = new Container($item);
                                    echo '<option value="' . $type->get('id') . '">';
                                    echo $type->get('name');
                                    echo '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div id="location"></div>
                </div>
            </div>
            
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        AdminPanelLocations();
    </script>
</html>

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
    $interface = new Container($languages->get('interface'));
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
            Resources::linkJS('adminPanelDocuments.js');
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
                        <?php echo $interface->get('documents'); ?>
                    </div>
                    <div>
                        <label class="standard-text">
                            <?php echo $interface->get('select_date'); ?>
                        </label>
                        <select class="standard-input" id="selectMonth">
                            <?php
                                $months = $languages->get('months');
                                foreach($months as $key => $month){
                                    echo '<option value="' . $key . '"';
                                    if($key === (int) date('m')){
                                        echo ' selected';
                                    }
                                    echo '>';
                                    echo $month;
                                    echo '</option>';
                                }
                            ?>
                        </select>
                        <select class="standard-input" id="selectYear">
                            <?php
                                $startYear = (int) $cfg->get('yearStart');
                                $endYear = ((int) date('Y')) + 1;
                                for($year = $startYear; $year <= $endYear; $year++){
                                    echo '<option value="' . $year . '"';
                                    if($year === (int) date('Y')){
                                        echo ' selected';
                                    }
                                    echo '>';
                                    echo $year;
                                    echo '</option>';
                                }
                            ?>
                        </select>
                        <button class="standard-button" id="selectDate">
                            <?php echo $interface->get('go_to'); ?>
                        </button>
                    </div>
                    <div id="documents"></div>
                </div>
                <div>
                    <div class="standard-text-title">
                        <?php echo $interface->get('assigned_users'); ?>
                    </div>
                    <div id="assignedUsers"></div>
                </div>
            </div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        AdminPanelDocuments();
    </script>
</html>

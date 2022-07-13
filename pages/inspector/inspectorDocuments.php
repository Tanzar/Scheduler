<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    use Tanweb\Security\PageAccess as PageAccess;
    
    PageAccess::allowFor(['admin', 'schedule_user_inspector']);   //locks access if failed to pass redirects to index page
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
            Resources::linkCSS('datatable.css');
            Resources::linkCSS('modal-box.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('modalBox.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('inspectorDocuments.js');
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
        <div class="page-contents">
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
                <div style="min-width: 300px">
                    <div class="standard-text-title">
                        <?php echo $interface->get('users_list'); ?>
                    </div>
                    <ul id="usersList"></ul>
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

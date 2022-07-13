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
            Resources::linkCSS('inspectionReportTable.css');
            Resources::linkCSS('datatable.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('inspectorReports.js');
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
            <div>
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
                <select id="selectDocument" class="standard-input"></select>
                <button id="generateReport" class="standard-button">
                    <?php echo $interface->get('generate_report'); ?>
                </button>
            </div>
            <div id="report">
                <table class="report">
                    <tr class="report-tr">
                        <td colspan="2" class="report-td">
                            <div class="standard-text">
                                <?php echo $interface->get('document_number'); ?>
                            </div>
                        </td>
                        <td colspan="2" class="report-td">
                            <div class="standard-text" id="documentNumber"></div>
                        </td>
                    </tr>
                    <tr class="report-tr">
                        <td class="report-td">
                            <div class="standard-text">
                                <?php echo $interface->get('start'); ?>
                            </div>
                        </td>
                        <td class="report-td">
                            <div class="standard-text" id="documentStart"></div>
                        </td>
                        <td class="report-td">
                            <div class="standard-text">
                                <?php echo $interface->get('end'); ?>
                            </div>
                        </td>
                        <td class="report-td">
                            <div class="standard-text" id="documentEnd"></div>
                        </td>
                    </tr>
                    <tr class="report-tr">
                        <td colspan="4" class="report-td">
                            <div class="horizontal-container">
                                <button class="standard-button" id="showHideUsers">
                                    <?php echo $interface->get('show'); ?>
                                </button>
                                <div class="standard-text">
                                    <?php echo $interface->get('assigned_users'); ?>
                                </div>
                            </div>
                            <div id="assignedUsersRow" style="display: none">
                                <div class="standard-text" id="assignedUsers"></div>
                            </div>
                        </td>
                    </tr>
                    <tr class="report-tr">
                        <td colspan="4" class="report-td">
                            <div class="horizontal-container">
                                <button class="standard-button" id="showHideEntries">
                                    <?php echo $interface->get('show'); ?>
                                </button>
                                <div class="standard-text">
                                    <?php echo $interface->get('assigned_entries'); ?>
                                </div>
                            </div>
                            <div id="assignedEntriesRow" style="display: none">
                                <div class="standard-text" id="assignedEntries"></div>
                            </div>
                        </td>
                    </tr>
                </table>
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
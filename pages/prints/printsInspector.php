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
    use Data\Access\Views\EquipmentDetailsView as EquipmentDetailsView;
    use Services\UserService as UserService;
    use Tanweb\Session as Session;
    
    PageAccess::allowFor(['admin', 'prints_inspector']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $security = Security::getInstance();
    $userService = new UserService();
    $equipmentDetails = new EquipmentDetailsView();
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
            Resources::linkJS('FileApi.js');
            Resources::linkJS('printsInspector.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php
            Scripts::run('printsSideMenu.php');
        ?>
        <div class="page-contents-centered">
            <div class="standard-text">
                <?php echo $interface->get('measurement_instrument'); ?>
            </div>
            <div class="horizontal-container">
                <select class="standard-input" id="selectInstrument">
                    <?php 
                        $instruments = $equipmentDetails->getActiveMeasurementInstruments();
                        foreach ($instruments->toArray() as $item) {
                            $instrument = new Container($item);
                            echo '<option value="' . $instrument->get('id') . '">';
                            echo $instrument->get('name');
                            echo '</option>';
                        }
                    ?>
                </select>
                <select class="standard-input" id="selectInstrumentYear">
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
                <button class="standard-button" id="instrumentUsageCard">
                    <?php echo $interface->get('instrument_usage_card'); ?>
                </button>
            </div>
            <div class="standard-text">
                <?php echo $interface->get('document_raport'); ?>
            </div>
            <div class="horizontal-container">
                <select class="standard-input" id="selectDocumentYear">
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
                <select id="selectDocumentIndex" class="standard-input"></select>
                <button id="generateDocumentReport" class="standard-button">
                    <?php echo $interface->get('generate_report'); ?>
                </button>
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

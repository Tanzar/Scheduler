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
                echo ' : ' . $modules->get('stats');
            ?>
        </title>
        <?php
            Resources::linkCSS('main.css');
            Resources::linkCSS('datatable.css');
            Resources::linkCSS('modal-box.css');
            Resources::linkJS('Datatable.js');
            Resources::linkJS('modalBox.js');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('FileApi.js');
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('statsDisplay.js');
            Resources::linkExternal('jquery');
            Resources::linkExternal('plotly');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('statsSideMenu.php'); ?>
        <div class="page-contents" id="display">
            <div class="horizontal-container">
                <div id="stats"></div>
                <div class="centered-contents-bordered" style="width: fit-content; min-width: 200px; height: fit-content">
                    <div class="standard-text">
                        <?php echo $interfaceText->get('inputs'); ?>
                    </div>
                    <div id="inputs"></div>
                    <button id="generateStats" class="standard-button" style="display: none"><?php echo $interfaceText->get('generate'); ?></button>
                    <div class="horizontal-container">
                        <button id="generatePDF" class="standard-button" style="display: none"><?php echo $interfaceText->get('generate_pdf_file'); ?></button>
                        <button id="generateXLSX" class="standard-button" style="display: none"><?php echo $interfaceText->get('generate_excel_file'); ?></button>
                    </div>
                </div>
                <div id="statsDisplay" class="standard-display" style="min-width: 600px"></div>
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

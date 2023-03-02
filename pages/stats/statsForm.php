<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\Template as Template;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    
    PageAccess::allowFor(['admin', 'stats_admin']);   //locks access if failed to pass redirects to index page
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
            Resources::linkJS('getRestAddress.js');
            Resources::linkJS('statsSettingsForm.js');
            Resources::linkExternal('jquery');
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
                <div class="centered-contents">
                    <div class="standard-text-title">
                        <?php echo $interfaceText->get('new_statistic'); ?>
                    </div>
                    <button id="savePattern" class="standard-button">
                        <?php echo $interfaceText->get('save'); ?>
                    </button>
                    <div class="horizontal-container">
                        <input type="text" id="statsName" placeholder="<?php echo $interfaceText->get('name'); ?>" limit="255" class="standard-input" required>
                        <input type="number" id="statsSort" placeholder="<?php echo $interfaceText->get('sort_priority'); ?>" min="1" class="standard-input" required>
                        <select id="selectPatternFile" class="standard-input" required>
                            <?php 
                                $templates = Template::listTemplates('stats');
                                echo '<option disabled placeholder selected value="">';
                                echo $interfaceText->get('select_pattern_file') . '</option>';
                                foreach ($templates->toArray() as $filename) {
                                    echo '<option value="' . $filename . '">';
                                    echo $filename . '</option>';
                                }
                            ?>
                        </select>
                        <input type="file" name="uploadPatternFile" id="uploadPatternFile" accept=".xlsx" class="standard-input" required>
                        <button id="uploadPattern" class="standard-button">
                            <?php echo $interfaceText->get('send'); ?>
                        </button>
                    </div>
                    <div class="horizontal-container">
                        <div class="standard-text"><?php echo $interfaceText->get('inputs') . ': '; ?></div>
                        <div class="standard-text" id="inputsDisplay"></div>
                        <button class="standard-button" id="setInputs"><?php echo $interfaceText->get('edit'); ?></button>
                    </div>
                    <div class="horizontal-container">
                        <div class="centered-contents">
                            <div class="standard-text">
                                <?php echo $interfaceText->get('columns'); ?>
                            </div>
                            <div id="columns"></div>
                        </div>
                        <div class="centered-contents">
                            <div class="standard-text">
                                <?php echo $interfaceText->get('rows'); ?>
                            </div>
                            <div id="rows"></div>
                        </div>
                        <div class="centered-contents">
                            <div class="standard-text">
                                <?php echo $interfaceText->get('cells'); ?>
                            </div>
                            <div id="cells"></div>
                        </div>
                    </div>
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

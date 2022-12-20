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
    
    PageAccess::allowFor(['admin', 'qualification_user']);   //locks access if failed to pass redirects to index page
    
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
                echo $cfg->get('name');
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('inventory');
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
            Resources::linkJS('qualificationEducation.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('qualificationSideMenu.php') ?>
        <div class="page-contents" id="display">
            <div class="horizontal-container">
                <div class="centered-contents">
                    <div class="standard-text">
                        <?php echo $interfaceText->get('persons'); ?>
                    </div>
                    <div class="horizontal-container">
                        <div class="standard-text">
                            <?php echo $interfaceText->get('search_by') . ": "; ?>
                        </div>
                        <input id="nameSearch" class="standard-input" placeholder="<?php echo $interfaceText->get('name_person'); ?>">
                        <input id="surnameSearch" class="standard-input" placeholder="<?php echo $interfaceText->get('surname'); ?>">
                        <button id="searchButton" class="standard-button"><?php echo $interfaceText->get('search');?></button>
                    </div>
                    <div id="persons"></div>
                </div>
                <div class="centered-contents">
                    <div class="standard-text">
                        <?php echo $interfaceText->get('schools'); ?>
                    </div>
                    <div id="schools"></div>
                </div>
                <div class="centered-contents">
                    <div class="standard-text">
                        <?php echo $interfaceText->get('courses'); ?>
                    </div>
                    <div id="courses"></div>
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

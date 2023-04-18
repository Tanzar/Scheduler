<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Services\UserService as UserService;
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
            Resources::linkJS('adminPanelDecisions.js');
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
                    <div class="standard-text-title"><?php echo $interfaceText->get('decisions'); ?></div>
                    <div class="horizontal-container">
                        <select id="selectUser" class="standard-input">
                            <option disabled placeholder selected><?php echo $interfaceText->get('select_user'); ?></option>
                            <?php 
                                $userservice = new UserService();
                                $users = $userservice->getAllInspectors();
                                foreach ($users->toArray() as $item){
                                    $user = new Container($item);
                                    echo '<option value="' . $user->get('username') . '">' .
                                            $user->get('username') . ' : ' . $user->get('name') .
                                            ' ' . $user->get('surname') . "</option>";
                                }
                            ?>
                        </select>
                        <?php 
                            Scripts::run('selectYear.php');
                        ?>
                    </div>
                    <div id="decisions"></div>
                </div>
                <div>
                    <div class="standard-text-title"><?php echo $interfaceText->get('decision_law'); ?></div>
                    <div id="decisionLaws"></div>
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

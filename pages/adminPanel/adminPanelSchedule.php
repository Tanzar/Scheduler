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
    use Services\UserService as UserService;
    
    PageAccess::allowFor(['admin']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $adminOptions = new Container($languages->get('admin'));
    $interfaceText = new Container($languages->get('interface'));
    $userservice = new UserService();
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
            Resources::linkJS('adminPanelSchedule.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('adminPanelSideMenu.php'); ?>
        <div class="page-contents" id="display">
            <div class="standard-text-title">
                <?php echo $interfaceText->get('entries'); ?>
            </div>
            <div>
                <label class="standard-text">
                    <?php echo $interfaceText->get('select_date'); ?>
                </label>
                <input type="date" id="selectDate" class="standard-input"
                           value="<?php echo date('Y-m-d'); ?>">
                <select id="selectUser" class="standard-input">
                    <option value="" selected disabled placeholder>
                        <?php echo $interfaceText->get('select_user'); ?>
                    </option>
                    <?php 
                        $users = $userservice->getAllUsers();
                        foreach ($users->toArray() as $item){
                            $user = new Container($item);
                            echo '<option value="' . $user->get('username') . '">';
                            echo $user->get('name') . ' ' . $user->get('surname');
                            echo '</option>';
                        }
                    ?>
                </select>
                <button id="selectDatasetButton" class="standard-button">
                    <?php echo $interfaceText->get('select'); ?>
                </button>
            </div>
            <br>
            <div id="entries"></div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        AdminPanelSchedule();
    </script>
</html>

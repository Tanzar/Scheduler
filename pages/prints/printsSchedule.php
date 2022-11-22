<?php 
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    use Tanweb\Security\Security as Security;
    use Services\UserService as UserService;
    use Tanweb\Session as Session;
    
    PageAccess::allowFor(['admin', 'prints_schedule', 'prints_schedule_reports']);   //locks access if failed to pass redirects to index page
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $security = Security::getInstance();
    $userService = new UserService();
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
            Resources::linkJS('printsSchedule.js');
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
            <div class="page-contents-element">
                <?php Scripts::run('selectMonthYearNoButton.php'); ?>
            </div>
            <div class="standard-text">
                <?php echo $interface->get('general'); ?>
            </div>
            <div class="horizontal-container">
                <button class="standard-button" id="attendanceList">
                    <?php echo $interface->get('attendance_list'); ?>
                </button>
                <button class="standard-button" id="notificationList">
                    <?php echo $interface->get('notification_list'); ?>
                </button>
                <?php 
                    if($security->userHaveAnyPrivilage(new Container(['admin', 'prints_schedule_reports']))){
                        echo '<button class="standard-button" id="nightShiftReport">';
                        echo $interface->get('monthly_night_shift_report');
                        echo '</button>';
                        }
                ?>
            </div>
            <div class="standard-text">
                <?php echo $interface->get('user_specific'); ?>
            </div>
            <div class="horizontal-container">
                <?php 
                    if($security->userHaveAnyPrivilage(new Container(['admin', 'schedule_admin']))){
                        echo '<select class="standard-input" id="selectUser">';
                        echo '<option placeholder disables selected value="' . 
                                Session::getUsername() . '">' . 
                                $interface->get('select_user') . '</option>';
                        $today = new DateTime();
                        $month = (int) $today->format('m');
                        $year = (int) $today->format('Y');
                        $users = $userService->getEmployedUsersListByMonthOrdered($month, $year);
                        foreach ($users->toArray() as $item){
                            $user = new Container($item);
                            echo '<option value="' . $user->get('username') . '">';
                            echo $user->get('name') . ' ' . $user->get('surname');
                            echo '</option>';
                        }
                        echo '</select>';
                    }
                ?>
                <button class="standard-button" id="timesheets">
                    <?php echo $interface->get('timesheets'); ?>
                </button>
                <button class="standard-button" id="workCard">
                    <?php echo $interface->get('work_card'); ?>
                </button>
            </div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        init(<?php echo '"' . Session::getUsername() . '"'; ?>);
    </script>
</html>

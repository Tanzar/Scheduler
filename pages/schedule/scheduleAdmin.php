<?php
    //--- use this atop every page for security if in PageAccess::allowFor() you pass empty array all will be allowed in
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    use Tanweb\Session as Session;
    use Services\UserService as UserService;
    use Services\ScheduleService as ScheduleService;
    use Tanweb\Utility as Utility;
    
    PageAccess::allowFor(['admin', 'schedule_admin']);   //locks access if failed to pass redirects to index page
    
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $security = new Security();
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
                echo $cfg->get('name');
                $modules = new Container($languages->get('modules'));
                echo ' : ' . $modules->get('schedule');
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
            Resources::linkJS('scheduleAdmin.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('scheduleSideMenu.php') ?>
        <div class="page-contents" id="pageContents">
            <div class="centered-contents">
                <select id="selectUser" class="standard-input">
                    <option value="" disabled selected placeholder>
                        <?php echo $interface->get('select_user'); ?>
                    </option>
                    <?php 
                        $users = $userservice->getEmployedUsersListOrdered(date('Y-m-d'));
                        foreach ($users->toArray() as $item){
                            $user = new Container($item);
                            echo '<option value="' . $user->get('username') . '">';
                            echo $user->get('name') . ' ' . $user->get('surname');
                            echo '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="horizontal-container">
                <div class="centered-contents">
                    <div class="standard-text-title">
                        <?php echo $interface->get('entries'); ?>
                    </div>
                    <div class="standard-text">
                        <?php echo $interface->get('select_date'); ?>
                        <input type="date" class="standard-input" id="displayDate" value="<?php echo date('Y-m-d'); ?>">
                        <button id="goTo" class="standard-button">
                            <?php echo $interface->get('go_to'); ?>
                        </button>
                    </div>
                    <div class="standard-text" id="rangeDisplay"></div>
                    <div id="entries"></div>
                </div>
                <div style="padding: 10px">
                    <div class="standard-text-title">
                        <?php echo $interface->get('new_entry'); ?>
                    </div>
                    <select id="selectActivityGroup" class="standard-input">
                        <?php
                            echo '<option placeholder, disabled selected>' . $interface->get('select_activity_group') . '</option>';
                            $scheduleService = new ScheduleService();
                            if($security->userHaveAnyPrivilage(new Container(['admin', 'schedule_admin', 'schedule_user_inspector']))){
                                $groups = $scheduleService->getAllActivityGroups();
                            }
                            else{
                                $groups = $scheduleService->getUserActivityGroups();
                            }
                            foreach ($groups->toArray() as $item){
                                echo '<option value="' . $item . '">' . $item . '</option>';
                            }
                        ?>
                    </select>
                    <br>
                    <select id="selectActivity" class="standard-input">
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_activity'); ?>
                        </option>
                    </select>
                    <br>
                    <select id="selectLocationType" class="standard-input">
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_location_type'); ?>
                        </option>
                    </select>
                    <br>
                    <select id="selectLocation" class="standard-input">
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_location'); ?>
                        </option>
                    </select>
                    <br>
                    <label class="standard-text"><?php echo $interface->get('start'); ?></label>
                    <input id="startDate" type="datetime-local" class="standard-input" value="<?php 
                            $username = Session::getUsername();
                            $period = $userservice->getUserCurrentEmploymentPeriod($username);
                            if($period->isEmpty()){
                                $date = date('Y-m-d');
                                $hour = 'T07:00';
                                echo date('Y-m-d') . 'T07:00';
                            }
                            else{
                                $time = Utility::getSubString($period->get('standard_day_start'), 0, 5);
                                $tim = date('Y-m-d') . 'T' . $time;
                                echo $tim;
                            }
                        ?>">
                    <br>
                    <label class="standard-text"><?php echo $interface->get('end'); ?></label>
                    <input id="endDate" type="datetime-local" class="standard-input" value="<?php 
                            if($period->isEmpty()){
                                echo date('Y-m-d') . 'T15:00';
                            }
                            else{
                                $time = Utility::getSubString($period->get('standard_day_end'), 0, 5);
                                $tim = date('Y-m-d') . 'T' . $time;
                                echo $tim;
                            }
                        ?>">
                    <br>
                    <button id="newEntry" class="standard-button">
                        <?php echo $interface->get('save'); ?>
                    </button>
                    <button id="addLocation" class="standard-button" style="display: none">
                        <?php echo $interface->get('new_location'); ?>
                    </button>
                </div>
            </div>
        </div>
        <?php
            Scripts::run('createFooter.php');
        ?>
    </body>
    <script>
        ScheduleAdmin();
    </script>
</html>

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
    use Tanweb\Security\Security as Security;
    use Tanweb\Session as Session;
    use Services\UserService as UserService;
    use Services\ScheduleService as ScheduleService;
    use Tanweb\Utility as Utility;
    
    PageAccess::allowFor(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);   //locks access if failed to pass redirects to index page
    
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $security = Security::getInstance();
    $username = Session::getUsername();
    $userservice = new UserService();
    $period = $userservice->getUserCurrentEmploymentPeriod($username);
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
            Resources::linkJS('scheduleMy.js');
            Resources::linkExternal('jquery');
        ?>
    </head>
    <body>
        <header>
            <?php Scripts::run('topMenu.php'); ?>
        </header>
        <?php Scripts::run('scheduleSideMenu.php') ?>
        <div class="page-contents" id="pageContents">
            <div class="horizontal-container">
                <div class="centered-contents">
                    <div class="standard-text-title">
                        <?php echo $interface->get('my_entries'); ?>
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
                    <select id="selectActivityGroup" class="standard-input" <?php if($period->isEmpty()) { echo 'disabled'; } ?>>
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
                    <select id="selectActivity" class="standard-input" <?php if($period->isEmpty()) { echo 'disabled'; } ?>>
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_activity'); ?>
                        </option>
                    </select>
                    <br>
                    <select id="selectLocationType" class="standard-input" <?php if($period->isEmpty()) { echo 'disabled'; } ?>>
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_location_type'); ?>
                        </option>
                    </select>
                    <br>
                    <select id="selectLocation" class="standard-input" <?php if($period->isEmpty()) { echo 'disabled'; } ?>>
                        <option value="" selected disabled placeholder>
                            <?php echo $interface->get('select_location'); ?>
                        </option>
                    </select>
                    <br>
                    <label class="standard-text"><?php echo $interface->get('start'); ?></label>
                    <input id="startDate" type="datetime-local" class="standard-input" <?php 
                            if($period->isEmpty()){
                                echo 'disabled';
                            }
                            else{
                                $time = Utility::getSubString($period->get('standard_day_start'), 0, 5);
                                $tim = date('Y-m-d') . 'T' . $time;
                                echo 'value="' . $tim . '"';
                                echo 'min="' . $period->get('start') . 'T00:00"';
                                echo 'max="' . $period->get('end') . 'T23:59"';
                            }
                        ?>>
                    <br>
                    <label class="standard-text"><?php echo $interface->get('end'); ?></label>
                    <input id="endDate" type="datetime-local" class="standard-input" <?php 
                            if($period->isEmpty()){
                                echo 'disabled';
                            }
                            else{
                                $time = Utility::getSubString($period->get('standard_day_end'), 0, 5);
                                $tim = date('Y-m-d') . 'T' . $time;
                                echo 'value="' . $tim . '"';
                                echo 'min="' . $period->get('start') . 'T00:00"';
                                echo 'max="' . $period->get('end') . 'T23:59"';
                            }
                        ?>>
                    <br>
                    <button id="newEntry" class="standard-button" <?php if($period->isEmpty()) { echo 'disabled'; } ?>>
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
        ScheduleMy();
    </script>
</html>

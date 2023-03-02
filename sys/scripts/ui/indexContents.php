<?php

/* 
 * This code is free to use, just remember to give credit.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Tanweb\Config\Scripts as Scripts;
use Tanweb\Session as Session;
use Services\IndexService as IndexService;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;
use Tanweb\Security\Security as Security;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;

$languages = Languages::getInstance();
$names = $languages->get('interface');
$interface = new Container($names);
$months = $languages->get('months');
$security = Security::getInstance();
$modules = new Container($languages->get('modules'));
$today = new DateTime(date('Y-m-d') . ' 00:00:00');
?>
<div class="horizontal-container"">
    <div class="centered-contents" style="min-width: 500px">
        <div class="standard-text-title">
            <?php 
                $view = new UsersWithoutPasswordsView();
                $username = Session::getUsername();
                $user = $view->getByUsername($username);
                echo $interface->get('welcome') . ' ' . $user->get('name') . ' ' . 
                        $user->get('surname') . ', ' . $interface->get('below_are_notifications_from_modules');
            ?>
        </div>
        <?php
        $userWithoutPrivilages = true;
        $adminPrivilages = new Container(['admin']);
        if($security->userHaveAnyPrivilage($adminPrivilages)){
            $userWithoutPrivilages = false;
            echo '<div class="centered-contents-bordered">';
            echo '<div class="standard-text-title">';
            echo $modules->get('admin');
            echo '</div>';
            echo '<div class="standard-text">';
            $locations = IndexService::getLocationsInTemporatyGroups();
            echo $interface->get('there_are') . ' ' . $locations->length() . ' ' . $interface->get('locations_in_tmp') . '<br>';
            $count = 1;
            for($i = $locations->length()- 1; $i >= 0 && $i >= $locations->length()- 10; $i--) {
                echo $count . ': ' . $locations->get($i) . '<br>';
                $count++;
            }
            echo '</div>';
            echo '</div>';
        }
        
        $schedulePrivilages = new Container(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);
        if($security->userHaveAnyPrivilage($schedulePrivilages)){
            $userWithoutPrivilages = false;
            echo '<div class="centered-contents-bordered">';
            echo '<div class="standard-text-title">';
            echo $modules->get('schedule');
            echo '</div>';
            echo '<div class="standard-text" style="color: red">';
            $scheduleBlockerDate = IndexService::getScheduleBlockerDate();
            $dateBeforeScheduleBlocker = new DateTime($scheduleBlockerDate->format('Y-m-d'));
            $dateBeforeScheduleBlocker->modify('-1 months');
            $monthBeforeScheduleBlocker = (int) $dateBeforeScheduleBlocker->format('m');
            echo $months[$monthBeforeScheduleBlocker] . ' ' .
                    $interface->get('will_close') . ' ' . $scheduleBlockerDate->format('Y-m-d');
            echo '</div>';
            echo '<div class="standard-text">';
            $days = IndexService::getDaysWithoutEntries();
            echo $interface->get('you_have') . ' ' . $days->length() . ' ' . $interface->get('days_without_entries') . '<br>';
            $count = 1;
            for($i = $days->length()- 1; $i >= 0 && $i >= $days->length()- 10; $i--) {
                echo $count . ': ' . $days->get($i) . '<br>';
                $count++;
            }
            echo '</div>';
            echo '</div>';
        }

        $inventoryPrivilages = new Container(['admin', 'inventory_user', 'inventory_admin']);
        if($security->userHaveAnyPrivilage($inventoryPrivilages)){
            $userWithoutPrivilages = false;
            echo '<div class="centered-contents-bordered">';
            echo '<div class="standard-text-title">';
            echo $modules->get('inventory');
            echo '</div>';
            echo '<div class="standard-text">';
            $unconfirmed = IndexService::getUnconfirmedEquipment();
            echo $interface->get('you_have') . ' ' . $unconfirmed->length() . ' ' . $interface->get('awaiting_equipment') . '<br>';
            $count = 1;
            for($i = 0; $i < $unconfirmed->length() && $i < 10; $i++) {
                $equipment = new Container($unconfirmed->get($i));
                echo $count . ': ' . $equipment->get('equipment_name') . '<br>';
                $count++;
            }
            echo '</div>';
            echo '</div>';
        }

        $inspectorPrivilages = new Container(['admin', 'schedule_user_inspector']);
        if($security->userHaveAnyPrivilage($inspectorPrivilages)){
            $userWithoutPrivilages = false;
            echo '<div class="centered-contents-bordered">';
            echo '<div class="standard-text-title">';
            echo $modules->get('inspector');
            echo '</div>';
            echo '<div class="standard-text" style="color: red">';
            $inspectorBlockerDate = IndexService::getInspectorBlockerDate();
            $dateBeforeInspectorBlocker = new DateTime($inspectorBlockerDate->format('Y-m-d'));
            $dateBeforeInspectorBlocker->modify('-1 months');
            $monthBeforeInspectorBlocker = (int) $dateBeforeInspectorBlocker->format('m');
            echo $months[$monthBeforeInspectorBlocker] . ' ' .
                    $interface->get('will_close') . ' ' . $inspectorBlockerDate->format('Y-m-d');
            echo '</div>';
            echo '<div class="standard-text">';
            $unassigned = IndexService::getUnassignedDecisions();
            echo $interface->get('you_have') . ' ' . $unassigned->length() . ' ' . $interface->get('unassigned_decision') . '<br>';
            $count = 1;
            for($i = 0; $i < $unassigned->length() && $i < 10; $i++) {
                $text = $unassigned->get($i);
                echo $count . ': ' . $text . '<br>';
                $count++;
            }
            echo '</div>';
            echo '</div>';
        }
        
        if($userWithoutPrivilages){
            echo '<div class="standard-text">';
            echo $interface->get('require_privilages');
            echo '</div>';
        }
        
        ?>
    </div>
    <div class="centered-contents">
        <div class="standard-text-title">
            <?php 
                echo $interface->get('my_data');
            ?>
        </div>
        <div>
            <?php Scripts::run('selectYear.php'); ?>
        </div>
        <div id="myData" class="centered-contents"></div>
    </div>
</div>
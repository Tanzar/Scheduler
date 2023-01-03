<?php

/* 
 * This code is free to use, just remember to give credit.
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Tanweb\Session as Session;
use Services\IndexService as IndexService;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;
use Tanweb\Security\Security as Security;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
    
$languages = Languages::getInstance();
$names = $languages->get('interface');
$interface = new Container($names);
$security = Security::getInstance();
$modules = new Container($languages->get('modules'));
?>
<div class="standard-text-title">
    <?php 
        $view = new UsersWithoutPasswordsView();
        $username = Session::getUsername();
        $user = $view->getByUsername($username);
        echo $interface->get('welcome') . ' ' . $user->get('name') . ' ' . 
                $user->get('surname') . ', ' . $interface->get('below_are_raports_from_modules');
    ?>
</div>
<?php
$schedulePrivilages = new Container(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);
if($security->userHaveAnyPrivilage($schedulePrivilages)){
    echo '<div class="standard-text-title">';
    echo $modules->get('schedule');
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
}


$inventoryPrivilages = new Container(['admin', 'inventory_user', 'inventory_admin']);
if($security->userHaveAnyPrivilage($inventoryPrivilages)){
    echo '<div class="standard-text-title">';
    echo $modules->get('inventory');
    echo '</div>';
    echo '<div class="standard-text">';
    $unconfirmed = IndexService::getUnconfirmedEquipment();
    echo $interface->get('you_have') . ' ' . $unconfirmed->length() . ' ' . $interface->get('unconfirmed_equipment') . '<br>';
    $count = 1;
    for($i = 0; $i < $unconfirmed->length() && $i < 10; $i++) {
        $equipment = new Container($unconfirmed->get($i));
        echo $count . ': ' . $equipment->get('equipment_name') . '<br>';
        $count++;
    }
    echo '</div>';
}
?>
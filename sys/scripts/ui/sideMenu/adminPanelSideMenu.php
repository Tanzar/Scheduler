<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    
    $language = Languages::getInstance();
    $adminOptions = new Container($language->get('admin'));
    $moduleNames = new Container($language->get('modules'));
    $interface = new Container($language->get('interface'));
?>
<div class="side-menu">
    <div class="standard-text">
        <?php echo $interface->get('main'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelSystem.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('system'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelUsers.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('users'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelDaysOff.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('free_days'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelOvertime.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('overtime'); ?>">
    </form>
    <div class="standard-text">
        <?php echo $moduleNames->get('schedule'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelSchedule.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('schedule'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelActivity.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('activities'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelLocation.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('locations'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelLocationSettings.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('locations_settings'); ?>">
    </form>
    <div class="standard-text">
        <?php echo $moduleNames->get('inspector'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelDocuments.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('documents'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelTicketSettings.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('ticket_settings'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelTicket.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('ticket'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelArticle.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('art_41'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelDecisions.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('decisions'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelSuspensionSettings.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('suspension_settings'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelSuspensions.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('suspensions'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelUsages.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('usages'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelCourt.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('court'); ?>">
    </form>
    <div class="standard-text">
        <?php echo $moduleNames->get('inventory'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelInventory.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('inventory'); ?>">
    </form>
    <div class="standard-text">
        <?php echo $moduleNames->get('qualifications'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelQualification.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('qualification'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelQualificationEducation.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('qualification_education'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('adminPanelQualificationSettings.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('qualification_settings'); ?>">
    </form>
    <div class="standard-text">
        <?php echo $moduleNames->get('prints'); ?>
    </div>
    <form action="<?php echo Pages::getURL('adminPanelPrints.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('prints'); ?>">
    </form>
</div>
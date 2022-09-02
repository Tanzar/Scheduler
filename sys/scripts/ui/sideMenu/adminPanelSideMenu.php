<?php
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    
    $language = Languages::getInstance();
    $adminOptions = new Container($language->get('admin'));
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('adminPanelUsers.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $adminOptions->get('users'); ?>">
    </form>
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
</div>
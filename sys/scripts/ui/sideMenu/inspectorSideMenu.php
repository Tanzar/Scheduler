<?php
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('inspector'));
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('inspectorReports.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('reports'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorDocuments.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('documents'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorMyDocuments.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('my_documents'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorTickets.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('tickets'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorArticle41.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('article_41'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorSuspensions.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('suspensions'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorDecisions.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('decisions'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorInstruments.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('instruments'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('inspectorCourtApplications.php') ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('court_applications'); ?>">
    </form>
</div>
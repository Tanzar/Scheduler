<?php
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('schedule'));
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('scheduleAll.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('all'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('scheduleMy.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('my'); ?>">
    </form>
    <?php
        $security = Security::getInstance();
        $privilages = new Container(['admin', 'schedule_admin']);
        if($security->userHaveAnyPrivilage($privilages)){;
            echo '<form action="' . Pages::getURL('scheduleAdmin.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('manage') . '">';
            echo '</form>';
        }
    ?>
</div>
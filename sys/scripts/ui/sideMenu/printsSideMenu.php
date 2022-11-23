<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    use Tanweb\Session as Session;
    use Tanweb\Config\Server as Server;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('prints'));
    $security = Security::getInstance();
?>
<div class="side-menu">
    <?php
        if($security->userHaveAnyPrivilage(new Container(array('admin', 'prints_schedule', 'prints_schedule_reports')))){
            echo '<form action="' . Pages::getURL('printsSchedule.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('schedule') . '">';
            echo '</form>';
        }
        if($security->userHaveAnyPrivilage(new Container(array('admin', 'prints_inspector')))){
            echo '<form action="' . Pages::getURL('printsInspector.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('inspector') . '">';
            echo '</form>';
        }
    ?>
</div>
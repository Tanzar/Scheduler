<?php
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    
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
        if($security->userHaveAnyPrivilage(new Container(array('admin', 'prints_schedule')))){
            echo '<form action="' . Pages::getURL('printsSchedule.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('schedule') . '">';
            echo '</form>';
        }
    ?>
</div>
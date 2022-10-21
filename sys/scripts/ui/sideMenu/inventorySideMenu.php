<?php
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('inventory'));
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('inventoryMy.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('my'); ?>">
    </form>
    <?php
        $security = Security::getInstance();
        $privilages = new Container(['admin', 'inventory_admin']);
        if($security->userHaveAnyPrivilage($privilages)){;
            echo '<form action="' . Pages::getURL('inventoryAdmin.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('admin') . '">';
            echo '</form>';
            echo '<form action="' . Pages::getURL('inventoryLog.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('log') . '">';
            echo '</form>';
        }
    ?>
</div>
<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('stats'));
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('statsDisplay.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('display'); ?>">
    </form>
    <?php
        $security = Security::getInstance();
        $privilages = new Container(['admin', 'siats_admin']);
        if($security->userHaveAnyPrivilage($privilages)){;
            echo '<form action="' . Pages::getURL('statsNoForm.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('settings_no_form') . '">';
            echo '</form>';
            echo '<form action="' . Pages::getURL('statsForm.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('settings_form') . '">';
            echo '</form>';
            echo '<form action="' . Pages::getURL('statsSUZUG.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('settings_suzug') . '">';
            echo '</form>';
        }
    ?>
</div>
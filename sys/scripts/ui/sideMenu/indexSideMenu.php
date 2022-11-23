<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    use Tanweb\Session as Session;
    use Tanweb\Config\Server as Server;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('index'));
?>
<div class="side-menu">
    <form action="<?php echo Server::getIndexPath() ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('index'); ?>">
    </form>
    <?php
        if(Session::getUsername() !== ''){
            echo '<form action="' . Pages::getURL('indexMyData.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('my_data') . '">';
            echo '</form>';
            
            echo '<form action="' . Pages::getURL('help.php') . '">';
            echo '<input type="submit" class="side-menu-button" value="' . $options->get('help') . '">';
            echo '</form>';
        }
    ?>
</div>
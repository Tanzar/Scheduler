<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    
    use Tanweb\Config\Pages as Pages;
    use Tanweb\Container as Container;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Security\Security as Security;
    use Tanweb\Session as Session;
    use Tanweb\Config\Server as Server;
    
    $language = Languages::getInstance();
    $options = new Container($language->get('qualification'));
    $security = Security::getInstance();
?>
<div class="side-menu">
    <form action="<?php echo Pages::getURL('qualificationMain.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('main'); ?>">
    </form>
    <form action="<?php echo Pages::getURL('qualificationEducation.php'); ?>">
        <input type="submit" class="side-menu-button" value="<?php  echo $options->get('education'); ?>">
    </form>
</div>
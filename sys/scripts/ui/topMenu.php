<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Session as Session;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\Server as Server;
    use Tanweb\Config\INI\AppConfig as AppConfig;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
    $appconfig = AppConfig::getInstance();
    $cfg = $appconfig->getAppConfig();
?>

<div class="top-menu">
    <div class="top-menu-left">
        <a href="<?php echo Server::getIndexPath(); ?>">
            <img src="<?php echo Resources::getIMG('logo.jpg'); ?>" title="<?php echo $cfg->get('organization_full'); ?>">
        </a>
        <?php
            Scripts::run('modulesLinks.php');
        ?>
    </div>
    <div class="top-menu-right">
        <div class="horizontal-container" style="margin: 5px">
            <div class="standard-text">
                <?php
                    echo Session::getUsername();
                ?>
            </div>
            <form action="<?php echo Scripts::get('logout.php'); ?>">
                <input type="submit" class="standard-button" value="<?php echo $interface->get('logout'); ?>">
            </form>
        </div>
        <div class="horizontal-container" style="margin: 5px">
            <div class="standard-text" style="margin: 2px">
                <?php echo $interface->get('language') . ': '; ?>
            </div>
            <form action="<?php echo Scripts::get('changeLanguage.php'); ?>" style="margin: 2px">
                <input type="hidden" name="language" value="polski">
                <input type="image" src="<?php echo Resources::getIMG('poland_flag.png') ?>" width="30px" height="30px" alt="submit" title="PL"/>
            </form>
            <form action="<?php echo Scripts::get('changeLanguage.php'); ?>" style="margin: 2px">
                <input type="hidden" name="language" value="english">
                <input type="image" src="<?php echo Resources::getIMG('united_kingdom_flag.png') ?>" width="30px" height="30px" alt="submit" title="ENG" />
            </form>
        </div>
    </div>
</div>
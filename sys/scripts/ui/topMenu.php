<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Session as Session;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
    $languages = Languages::getInstance();
    $names = $languages->get('interface');
    $interface = new Container($names);
?>

<div class="top-menu">
    <div class="top-menu-left">
        <?php
            Resources::linkIMG('logo-alt.jpg', 'logo_img');
            Scripts::run('modulesLinks.php');
        ?>
    </div>
    <div class="top-menu-right">
        <?php
            echo Session::getUsername() . '<br>';
        ?>
        <form action="<?php echo Scripts::get('changeLanguage.php'); ?>">
            <?php echo $interface->get('language') . ': '; ?>
            <select name="language" class="standard-input">
                <?php
                    $options = Languages::getLanguageOptions();
                    foreach ($options as $option){
                        echo '<option value="' . $option . '" '
                                . 'class="standard-button">' ;
                        echo $option;
                        echo '</option>';
                    }
                ?>
            </select>
            <input type="submit" class="standard-button" value="<?php echo $interface->get('save'); ?>">
            
        </form>
        <form action="<?php echo Scripts::get('logout.php'); ?>">
            <input type="submit" class="standard-button" value="<?php echo $interface->get('logout'); ?>">
        </form>
    </div>
</div>
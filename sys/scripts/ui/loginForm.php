<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
    
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
    $languages = Languages::getInstance();
    $interface = $languages->get('interface');
    $interfaceNames = new Container($interface);
?>

<form class="login-form" action="<?php echo Scripts::get('login.php'); ?>" method="post">
    <input class="login-form-input" name="username" type="text" placeholder="<?php echo $interfaceNames->get('username'); ?>">
    <input class="login-form-input" name="password" type="password" placeholder="<?php echo $interfaceNames->get('password'); ?>">
    <input class="standard-button" type="submit" value="<?php echo $interfaceNames->get('login'); ?>">
</form>
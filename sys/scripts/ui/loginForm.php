<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Config\INI\Languages as Languages;
    use Tanweb\Container as Container;
    
    $languages = Languages::getInstance();
    $interface = $languages->get('interface');
    $interfaceNames = new Container($interface);
?>

<form class="login-form" action="<?php echo Scripts::get('login.php'); ?>">
    <input class="login-form-input" name="username" type="text" placeholder="<?php echo $interfaceNames->getValue('username'); ?>">
    <input class="login-form-input" name="password" type="password" placeholder="<?php echo $interfaceNames->getValue('password'); ?>">
    <input class="login-form-submit" type="submit" value="<?php echo $interfaceNames->getValue('login'); ?>">
</form>
<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Config\Scripts as Scripts;
?>

<form class="login-form" action="<?php echo Scripts::get('login.php'); ?>">
    <a>Zaloguj</a>
    <input class="login-form-input" name="username" type="text" placeholder="nazwa użytkownika">
    <input class="login-form-input" name="password" type="password" placeholder="hasło">
    <input class="login-form-submit" type="submit" value="Zaloguj">
</form>
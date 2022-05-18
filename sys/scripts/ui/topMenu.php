<?php

/* 
 * This code is free to use, just remember to give credit.
 */
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Session as Session;
    use Tanweb\Container as Container;
    use Tanweb\Config\Resources as Resources;
    use Tanweb\Config\Scripts as Scripts;
    use Tanweb\Database\Database as Database;
    use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
    use Tanweb\Security\Security as Security;
?>

<div class="top-menu">
    <div class="top-menu-left">
        <?php
            Resources::linkIMG('logo.jpg', 'logo_img');
            Scripts::run('modulesLinks.php');
        ?>
    </div>
    <div class="top-menu-right">
        <?php
            echo Session::getUsername() . '<br>';
        ?>
        <form action="<?php echo Scripts::get('logout.php'); ?>">
            <input type="submit" value="Wyloguj">
        </form>
        <form action="<?php echo Scripts::get('login.php'); ?>">
            <select name="username">
                <?php
                    $database = new Database('scheduler');
                    $sql = new MysqlBuilder();
                    $sql->select('user');
                    $users = $database->select($sql);
                    foreach ($users->toArray() as $user){
                        $container = new Container($user);
                        $displayName = $container->getValue('surname');
                        $username = $container->getValue('username');
                        echo '<option value="' . $username . '">';
                        echo $displayName . '</option>';
                    }
                ?>
            </select>
            <input type="submit" value="Zaloguj">
        </form>
    </div>
</div>
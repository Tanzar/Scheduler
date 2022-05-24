<?php 
    
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
 
?>
<!DOCTYPE html>
<!--
This code is free to use, just remember to give credit.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
    </body>
    <script>
    </script>
</html>

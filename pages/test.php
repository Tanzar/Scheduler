<?php 
    
    session_start();
    $projectName = explode('/', $_SERVER['REQUEST_URI'])[1];
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . $projectName . '/vendor/autoload.php';
    
    use Tanweb\Security\PageAccess as PageAccess;
    
    PageAccess::allowFor(['admin']);   //locks access if failed to pass redirects to index page
    
    use Tanweb\Config\Scripts as Scripts;
    
    Scripts::run('logPageAccess.php');
?>
<!DOCTYPE html>
<!--
This code is free to use, just remember to give credit.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        <?php
            use Tanweb\Config\Resources as Resources;
        
            Resources::linkCSS('main.css');
            Resources::linkExternal('jquery');
            Resources::linkJS('RestApi.js');
            Resources::linkJS('FileApi.js');
        ?>
    </head>
    <body>
        <br><br>
        <button onclick="test()">Test rest</button><br>
        <button onclick="testFile()">Test file</button>
    </body>
    <script>
            function testFile(){
                FileApi.post('TestFile', 'test', { field: 'test', number: 10});
            }
            function test(){
                RestApi.post('Test', 'test', { field: 'test', number: 10}, function(response){
                    var data = JSON.parse(response);
                    console.log(data);
                });
            }
    </script>
</html>

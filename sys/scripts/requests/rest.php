<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use Tanweb\Request\Request as Request;

try{
    $request = new Request();
    
    $className = 'Controllers\\' . $request->getController();
    if(method_exists($className, '__construct')){
        $controller = new $className;
    }
    else{
        throw new Exception('rest.php error: Controller ' . $className . ' not defined.');
    }
    
    if(!is_subclass_of($controller, 'Controllers\Base\Controller')){
        throw new Exception('rest.php error: Controller must extend class Controllers\Base\Controller.');
    }
    
    if(!is_callable(array($controller, 'run'))){
        throw new Exception('rest.php error: class Controllers\Base\Controller modified or overriden.');
    }
    
    $response = $controller->run($request);
    
    if($response->isEnabled()){
        $data = $response->toArray();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
} catch (Throwable $ex) {
    header("HTTP/1.0 500");
    exit($ex->getMessage());
}

?>
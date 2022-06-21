<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers\Base;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Container as Container;
use Tanweb\Database\Database as Database;
use Tanweb\Request\Request as Request;
use Tanweb\Request\Response as Response;
use Tanweb\Security\Security as Security;
use Tanweb\TanwebException as TanwebException;
use Controllers\Base\ControllerException as ControllerException;
use Throwable;

/**
 * Class must be extended by ALL controllers to be properly managed.
 * Class should be used to call certain services.
 * You should not do complicated processes in it, like accessing databases.
 * This class also manages errors 
 * if your classes inside throw any exception Controller will catch them
 * 
 * To call methods in controllers you must pass data for: 
 * - controller (your controller name)
 * - task (method name)
 * to scripts:
 * - rest.php (rest api)
 * - file.php (managing request with file as result)
 * 
 * Best is to use javascripts from FileApi.js and RestApi.js 
 * and pass correct parameters
 * 
 * For example:
 * You create controller named Car with mehtod getModels()
 * Car must extend Controller to work
 * to call it you must send as data its name and method name
 * JSON {controller: 'Car', task 'getModels(), brand : 'Audi' }
 * On webpage use script from file RestApi.js like:
 * RestApi.post(
 *          'Car',
 *          'getModels',
 *          {controller: 'Car', task 'getModels(), brand : 'Audi'}, 
 *          function(response){
 *              //js code here to manage recieved data
 *          }, function(response){
 *              //handle errors
 * });
 * 
 * you can access variables you send by calling $this->getRequestData()
 * 
 * to restrict access to controller add required privilages in constructor using container class
 * 
 * parent::__construct(array(<privilage names>));
 * 
 * if you call controller with FileApi or file.php than 
 * response won't work and you will not recieve any, 
 * it is working for file sending using ExcelEditor and PDFMaker
 * 
 *
 * @author Grzegorz Spakowski, Tanzar
 */
abstract class Controller {
    private Request $request;
    private Response $response;
    private Security $security;
    private Container $requiredPrivilages;
    
    public function __construct(Container $privilages = null) {
        $this->databases = new Container();
        $this->response = new Response();
        $this->security = new Security();
        if(isset($privilages)){
            $this->requiredPrivilages = $privilages;
        }
        else{
            $this->requiredPrivilages = new Container();
        }
    }
    
    public function run(Request $request) : Response{
        try{
            $privilages = $this->requiredPrivilages;
            $this->security->checkPrivilages($privilages);
            return $this->runTask($request);
        } catch (TanwebException $ex) {
            $this->rollbackAll();
            $msg = $ex->errorMessage();
            $logger = Logger::getInstance();
            $logger->logError($msg);
            throw $ex;
        } catch (Throwable $ex) {
            $this->rollbackAll();
            $msg = $ex->getMessage() . " ;\n " .$ex->getTraceAsString();
            $logger = Logger::getInstance();
            $logger->logError($msg);
            throw $ex;
        }
    }
    
    private function runTask(Request $request) : Response {
        $this->request = $request;
        $task = $this->request->getTask();
        if(is_callable(array($this, $task))){
            $msg = $request->toJSON();
            $logger = Logger::getInstance();
            $logger->logRequest($msg);
            $this->$task();
        }
        else{
            $this->throwException('method ' . $task . '() not defined.');
        }
        $this->finalizeTransactions();
        return $this->response;
    }
    
    private function finalizeTransactions() : void{
        Database::finalizeAll();
    }
    
    private function rollbackAll() : void {
        Database::rollbackAll();
    }
    
    public function disableResponse(){
        $this->response->disable();
    }
    
    protected function getRequestData() : Container{
        return $this->request->get();
    }
    
    protected function setResponse(Container $container){
        $this->response->overrideData($container);
    }
    
    protected function getConfigValue(string $index){
        $appConfig = AppConfig::getInstance();
        $config = $appConfig->getAppConfig();
        return $config->get($index);
    }
    
    protected function currentUserHavePrivilage(string $privilage) : bool {
        return $this->security->userHavePrivilage($privilage);
    }
    
    protected function throwException($msg){
        throw new ControllerException($msg);
    }
}

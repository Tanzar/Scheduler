<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers\Base;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Logger\Entry\LogEntry as LogEntry;
use Tanweb\Container as Container;
use Tanweb\Database\Database as Database;
use Tanweb\Database\SQL\SqlBuilder as SqlBuilder;
use Tanweb\Request\Request as Request;
use Tanweb\Request\Response as Response;
use Tanweb\Security\Security as Security;
use Tanweb\TanwebException as TanwebException;
use Controllers\Base\ControllerException as ControllerException;
use Throwable;

/**
 * In short "heart of application"
 * Class must be extended by ALL controllers to be properly managed.
 * Custom controllers can:
 *  use protected methods to 
 * a) manage response,
 * b) access databases,
 * c) access configs,
 * d) use custom logs
 * Class by itself manages most tasks like 
 * initializing databases, managing access, logging  basic informations.
 * 
 * To call methods in controllers you must pass data for: 
 * - controller (your controller name)
 * - task (method name)
 * to scripts:
 * - rest.php (rest api)
 * - file.php (managing request with file as result)
 * 
 * Best is to use javascripts from FileApi.js and RestApi.js and pass correct parameters
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
 *              //js code here if reqeust is successfull
 *          }, function(response){
 *              //if error occurred
 * });
 * 
 * you can access variable brand in your controller using method $this->readFromRequest('brand');
 * 
 * if you need database access configure database it in config.ini file, 
 * than in constructor put dbIndex in array() and 
 * pass it as parameter to parent::__construct($databases);
 * 
 * to restrict access to controller add required privilages in constructor like
 * parent::__construct(null, array(<privilage names>));
 * 
 * if you call controller with FileApi or file.php than 
 * response won't work and you will not recieve any, 
 * it is working for file sending using ExcelEditor and PDFMaker
 * 
 *
 * @author Grzegorz Spakowski, Tanzar
 */
abstract class Controller {
    private AppConfig $appConfig;
    private Logger $logger;
    private Container $databases;
    private Request $request;
    private Response $response;
    private Security $security;
    private Container $requiredPrivilages;
    
    public function __construct(Container $indexes = null, Container $privilages = null) {
        $this->logger = new Logger();
        $this->databases = new Container();
        $this->response = new Response();
        $this->appConfig = new AppConfig();
        if(isset($indexes)){
            $this->initializeDatabases($indexes);
        }
        $this->security = new Security($this->logger);
        if(isset($privilages)){
            $this->requiredPrivilages = $privilages;
        }
        else{
            $this->requiredPrivilages = new Container();
        }
    }
    
    private function initializeDatabases(Container $indexes){
        foreach ($indexes->toArray() as $index){
            $database = new Database($index);
            $this->databases->add($database, $index);
        }
    }
    
    public function run(Request $request) : Response{
        try{
            $privilages = $this->requiredPrivilages;
            if($this->security->userHaveAnyPrivilage($privilages)){
                return $this->runTask($request);
            }
            else{
                $this->logger->logSecurity('Access denied.');
            }
        } catch (TanwebException $ex) {
            $this->rollbackAll();
            $msg = $ex->errorMessage();
            $this->logger->logError($msg);
            throw $ex;
        } catch (Throwable $ex) {
            $this->rollbackAll();
            $msg = $ex->getMessage() . " ;\n " .$ex->getTraceAsString();
            $this->logger->logError($msg);
            throw $ex;
        }
    }
    
    private function runTask(Request $request) : Response {
        $this->request = $request;
        $task = $this->request->getTask();
        if(is_callable(array($this, $task))){
            $msg = $request->toJSON();
            $this->logger->logRequest($msg);
            $this->$task();
        }
        else{
            $this->throwException('method ' . $task . '() not defined.');
        }
        $this->finalizeTransactions();
        return $this->response;
    }
    
    private function finalizeTransactions() : void{
        foreach ($this->databases->toArray() as $database){
            $database->finalize();
        }
    }
    
    private function rollbackAll() : void {
        foreach ($this->databases->toArray() as $database){
            $database->rollback();
        }
    }
    
    public function disableResponse(){
        $this->response->disable();
    }
    
    protected function readFromRequest(string $field){
        return $this->request->get($field);
    }
    
    protected function setResponse(Container $container){
        $this->response->overrideData($container);
    }
    
    protected function select($dbIndex, SqlBuilder $sql) : Container{
        $query = $sql->formSQL();
        $this->logger->logSelect($query);
        $db = $this->databases->getValue($dbIndex);
        return $db->select($sql);
    }
    
    protected function insert($dbIndex, SqlBuilder $sql){
        $query = $sql->formSQL();
        $this->logger->logInsert($query);
        $db = $this->databases->getValue($dbIndex);
        return $db->insert($sql);
    }
    
    protected function update($dbIndex, SqlBuilder $sql){
        $query = $sql->formSQL();
        $this->logger->logUpdate($query);
        $db = $this->databases->getValue($dbIndex);
        $db->update($sql);
    }
    
    protected function log(LogEntry $entry){
        $this->logger->log($entry);
    }

    protected function getConfigValue(string $index){
        $config = $this->appConfig->getAppConfig();
        return $config->getValue($index);
    }
    
    protected function getUser() : Container {
        
    }
    
    protected function throwException($msg){
        throw new ControllerException($msg);
    }
    
}

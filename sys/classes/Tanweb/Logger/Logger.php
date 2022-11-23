<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger;

use Tanweb\Logger\Entry\LogEntry as LogEntry;
use Tanweb\Logger\Entry\AccessEntry as AccessEntry;
use Tanweb\Logger\Entry\ErrorEntry as ErrorEntry;
use Tanweb\Logger\Entry\InsertEntry as InsertEntry;
use Tanweb\Logger\Entry\RequestEntry as RequestEntry;
use Tanweb\Logger\Entry\SecurityEntry as SecurityEntry;
use Tanweb\Logger\Entry\SelectEntry as SelectEntry;
use Tanweb\Logger\Entry\UpdateEntry as UpdateEntry;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Config\Server as Server;
use Tanweb\Container as Container;
use Tanweb\Database\Database as Database;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Utility as Utility;
use Tanweb\Logger\LoggerException as LoggerException;

/**
 * Object responsible for creating and managing application logs, 
 * can be configured in config.ini 
 * if database is enabled it requires table with columns:
 * 'timestamp' timestamp
 * 'username' text
 * 'message' text
 * 'entry_type' text
 * 'ip' text
 * 'proxy' text
 * for file it creates file in folder logs
 * 
 * 
 * @author Grzegorz Spakowski, Tanzar
 */
class Logger {
    private static Logger $instance;
    
    private bool $isEnabled;
    private array $activeTypes;
    
    private bool $saveLocal;
    private string $targetFile;
    
    private bool $saveDatabase;
    private Database $database;
    private string $table;
    
    
    protected function __construct(){
        $appconfig = AppConfig::getInstance();
        $config = $appconfig->getLogger();
        $this->isEnabled = $config->get('enable');
        if($this->isEnabled){
            $this->loadConfig($config, $appconfig);
        }
    }
    
    private function loadConfig(Container $config, AppConfig $appConfig) : void {
        $this->loadTypes($config);
        $this->loadLocalConfig($config, $appConfig);
        $this->loadDatabaseConfig($config);
    }
    
    private function loadTypes(Container $config) : void {
        $types = $config->get('log');
        $this->activeTypes = array();
        foreach ($types as $name => $isActive){
            if($isActive){
                $this->activeTypes[] = $name;
            }
        }
    }
    
    private function loadLocalConfig(Container $config, AppConfig $appConfig) : void {
        $this->saveLocal = $config->get('local');
        if($this->saveLocal === true){
            $this->checkLogFile($appConfig);
        }
        else{
            $this->saveLocal = false;
        }
    }
    
    private function checkLogFile(){
        $dir = Server::getLocalRoot() . '/logs';
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $date = date('d-m-Y');
        $path = $dir . '/' . $date . '.log';
        $this->targetFile = $path;
    }
    
    private function loadDatabaseConfig(Container $config) : void {
        $this->saveDatabase = $config->get('database');
        if($this->saveDatabase === true){
            $dbIndex = $config->get('database_index');
            $this->database = new Database($dbIndex);
            $this->table = $config->get('database_table');
            $this->verifyDatabase();
        }
        else{
            $this->saveDatabase = false;
        }
    }
    
    private function verifyDatabase(){
        $checklist = $this->makeChecklist();
        $columns = $this->getTargetTableInfo();
        
        foreach($columns as $column){
            foreach($checklist as $key => $item){
                $dataTypeCol = $column['DATA_TYPE'];
                $dataTypeChk = $item['DATA_TYPE'];
                $nameCol = $column['COLUMN_NAME'];
                $nameChk = $item['COLUMN_NAME'];
                if($dataTypeCol === $dataTypeChk && $nameCol === $nameChk){
                    $checklist[$key]['check'] = true;
                }
            }
        }
        $this->verifyChecklist($checklist);
    }
    
    private function makeChecklist(){
        return array(
            array(
                'COLUMN_NAME' => 'timestamp',
                "DATA_TYPE" => 'timestamp',
                'check' => false
            ),
            array(
                'COLUMN_NAME' => 'username',
                "DATA_TYPE" => 'text',
                'check' => false
            ),
            array(
                'COLUMN_NAME' => 'message',
                "DATA_TYPE" => 'text',
                'check' => false
            ),
            array(
                'COLUMN_NAME' => 'entry_type',
                "DATA_TYPE" => 'text',
                'check' => false
            ),
            array(
                'COLUMN_NAME' => 'ip',
                "DATA_TYPE" => 'text',
                'check' => false
            ),
            array(
                'COLUMN_NAME' => 'proxy',
                "DATA_TYPE" => 'text',
                'check' => false
            ),
        );
    }
    
    private function getTargetTableInfo(){
        $sql = new MysqlBuilder();
        $sql->select('INFORMATION_SCHEMA.COLUMNS', 
                array('COLUMN_NAME', 'DATA_TYPE'));
        $sql->where('TABLE_NAME', $this->table);
        $data = $this->database->select($sql);
        return $data->toArray();
    }
    
    private function verifyChecklist(array $checklist){
        foreach ($checklist as $item){
            if(!$item['check']){
                $this->throwException('check error table in wrong format.');
            }
        }
    }
    
    public function __destruct() {
        if(isset($this->database)){
            $this->database->finalize();
        }
    }
    
    public static function getInstance() : Logger{
        if(isset(self::$instance)){
            return self::$instance;
        }
        else{
            self::$instance = new Logger();
            return self::$instance;
        }
    }
    
    public function logAccess(string $msg) : void {
        $entry = new AccessEntry($msg);
        $this->log($entry);
    }
    
    public function logError(string $msg) : void {
        $entry = new ErrorEntry($msg);
        $this->log($entry);
    }
    
    public function logInsert(string $msg) : void {
        $entry = new InsertEntry($msg);
        $this->log($entry);
    }
    
    public function logRequest(string $msg) : void {
        $entry = new RequestEntry($msg);
        $this->log($entry);
    }
    
    public function logSecurity(string $msg) : void {
        $entry = new SecurityEntry($msg);
        $this->log($entry);
    }
    
    public function logSelect(string $msg) : void {
        $entry = new SelectEntry($msg);
        $this->log($entry);
    }
    
    public function logUpdate(string $msg) : void {
        $entry = new UpdateEntry($msg);
        $this->log($entry);
    }
    
    public function log(LogEntry $entry) : void{
        if($this->isEnabled){
            $this->logEntry($entry);
        }
    }
    
    private function logEntry(LogEntry $entry) : void {
        if($this->isActiveType($entry)){
            $this->logToDatabase($entry);
            $this->logToFile($entry);
        }
    }
    
    private function isActiveType(LogEntry $entry) : bool{
        $type = $entry->getType();
        $typeLC = Utility::toLowerCase($type);
        if(in_array($typeLC, $this->activeTypes)){
            return true;
        }
        return false;
    }
    
    private function logToDatabase(LogEntry $entry){
        if($this->saveDatabase){
            $sql = new MysqlBuilder();
            $sql->insert($this->table)->into('entry_type', $entry->getType());
            $sql->into('username', $entry->getUser());
            $sql->into('timestamp', $entry->getTimestamp());
            $sql->into('message', $entry->getMessage());
            $sql->into('ip', $entry->getIp());
            $sql->into('proxy', $entry->getProxy());
            $this->database->insert($sql);
        }
    }
    
    private function logToFile(LogEntry $entry){
        if($this->saveLocal){
            $file = fopen($this->targetFile, 'a');
            $line = '[' . $entry->getTimestamp() . '] ';
            $line .= '[type: ' . $entry->getType() . '] ';
            $line .= '[user: ' . $entry->getUser() . '] ';
            $line .= '[ip: ' . $entry->getIP() . '] ';
            $line .= '[proxy: ' . $entry->getProxy() . '] ';
            $line .= $entry->getMessage() . "\n";
            fwrite($file, $line);
            fclose($file);
        }
    }
    
    private function throwException($msg){
        if($this->database){
            $this->database->rollback();
        }
        throw new LoggerException('Logger error: ' . $msg);
    }
    
}

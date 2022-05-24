<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database;

use \PDO;
use Exception;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Database\SQL\SqlBuilder as SqlBuilder;
use Tanweb\Database\ConnectionString as ConnectionString;
use Tanweb\Database\DatabaseException as DatabaseException;

/**
 * Class responsible for managing database connection.
 * Upon creation it starts transaction, 
 * than methods select, insert, update allow to use SQLBuilder to create Queries
 * Methods finalize and rollback are (should be) calld at end of transaction
 * (managed by Controller class, see it for more info)
 * databases can be managed in config.ini
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Database {
    private static Container $instances;
    
    private Container $config;
    private PDO $pdo;
    
    public function __construct(string $index) {
        $config = AppConfig::getInstance();
        $this->config = $config->getDatabase($index);
        try{
            $this->connect();
        } catch (Exception $ex) {
            $this->throwException('error initializing connection: ' . 
                    $ex->getMessage());
        }
    }
    
    private function connect(){
        $connectionString = ConnectionString::create($this->config);
        $user = $this->config->getValue('user');
        $pass = $this->config->getValue('pass');
        $pdo = new PDO($connectionString, $user, $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->beginTransaction();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo = $pdo;
    }
    
    public static function getInstance(string $dbIndex) : Database{
        if(isset(self::$instances)){
            if(self::$instances->isValueSet($dbIndex)){
                return self::$instances->getValue($dbIndex);
            }
            else{
                $instance = new Database($dbIndex);
                self::$instances->add($instance, $dbIndex);
                return $instance;
            }
        }
        else{
            self::$instances = new Container();
            $instance = new Database($dbIndex);
            self::$instances->add($instance, $dbIndex);
            return $instance;
        }
    }
    
    public static function rollbackAll() : void{
        $instances = self::$instances->toArray();
        foreach($instances as $database){
            $database->rollback();
        }
    }
    
    public static function finalizeAll() : void{
        $instances = self::$instances->toArray();
        foreach($instances as $database){
            $database->finalize();
        }
    }
    
    public function rollback(){
        $this->pdo->rollBack();
    }
    
    public function finalize(){
        $this->pdo->commit();
    }
    
    /**
     * Method for using select Queries
     * 
     * @param SqlBuilder $builder - select query
     * @return Container - result of query
     */
    public function select(SqlBuilder $builder) : Container{
        if($builder->isSelect()){
            $sql = $builder->formSQL();
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            $data = $statement->fetchAll(PDO::FETCH_ASSOC);
            if($data){
                return new Container($data);
            }
            else{
                return new Container();
            }
        }
        else{
            $this->throwException('wrong builder, must be select, was: ' . $builder->getType());
        }
    }
    
    /**
     * Method for using insert Queries
     * 
     * @param SqlBuilder $builder - insert Query
     * @return int - id of inserted element (must be int)
     */
    public function insert(SqlBuilder $builder) : int {
        if($builder->isInsert()){
            $sql = $builder->formSQL();
            $statement = $this->pdo->prepare($sql);
            $haveInserted = $statement->execute();
            if($haveInserted){
                return $this->pdo->lastInsertId();
            }
            else{
                $this->throwException('insert error for sql: ' . $sql);
            }
        }
        else{
            $this->throwException('wrong builder, must be insert, was: ' . $builder->getType());
        }
    }
    
    /**
     * Method for update queries
     * ! IMPORTANT !
     * method have "safety check" in form of checking if more than 1 row is affected,
     * if you want to update mutliple rows call this method multiple times
     * every query must have where condition to make it update only single row
     * 
     * @param SqlBuilder $builder
     */
    public function update(SqlBuilder $builder) {
        if($builder->isUpdate()){
            $sql = $builder->formSQL();
            $statement = $this->pdo->prepare($sql);
            $haveFailed = !($statement->execute());
            $affected = $statement->rowCount();
            if($haveFailed){
                $this->throwException('update failed for sql: ' . $sql);
            }
            if($affected > 1){
                $this->throwException('sefety error, update should only affect '
                        . '1 row at most, affected: ' . $affected);
            }
        }
        else{
            $this->throwException('wrong builder, must be update, was: ' . $builder->getType());
        }
    }
    
    private function throwException($msg){
        throw new DatabaseException($msg);
    }
}

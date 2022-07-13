<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\SQL\SqlBuilder as SqlBuilder;
use Tanweb\Database\Database as Database;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Database\DatabaseException as DataAccessException;

/**
 * Description of DataObject
 *
 * @author Tanzar
 */
abstract class DataAccessObject {
    private string $dbIndex;
    private string $table;
    
    protected function __construct() {
        $dbIndex = $this->setDatabaseIndex();
        $this->dbIndex = $dbIndex;
        $this->table = $this->setDefaultTable();
    }
    
    protected abstract function setDatabaseIndex() : string;
    
    protected abstract function setDefaultTable() : string;
    
    public function getAll() : Container {
        $sql = new MysqlBuilder();
        $sql->select($this->table);
        return $this->select($sql);
    }
    
    public function getById(int $id) : Container{
        $sql = new MysqlBuilder();
        $sql->select($this->table)->where('id', $id);
        $resultset = $this->select($sql);
        if($resultset->length() > 1){
            $this->throwIdColumnException($this->table);
        }
        if($resultset->length() === 0){
            $this->throwNotFoundException($this->table);
        }
        $result = new Container($resultset->get(0));
        return $result;
    }
    
    public function save(Container $item) : int {
        if($item->isValueSet('id')){
            $this->change($item);
            $id = $item->get('id');
            return (int) $id;
        }
        else{
            return $this->add($item);
        }
    }
    
    public function change(Container $item) : void {
        $id = (int) $item->get('id');
        $old = $this->getById($id);
        try{
            $sql = $this->buildUpdateSQL($old, $item);
            $this->update($sql);
        } catch (DataAccessException $ex) {
        }
    }
    
    private function buildUpdateSQL(Container $old, Container $new) : MysqlBuilder{
        $sql = new MysqlBuilder();
        $id = $old->get('id');
        $sql->update($this->table, 'id', $id);
        $keys = $old->getKeys();
        $somethingChanged = false;
        foreach ($keys as $key){
            if($new->isValueSet($key) && $key !== 'id' && $this->valuesAreDifferent($key, $old, $new)){
                $sql->set($key, $new->get($key));
                $somethingChanged = true;
            }
        }
        if($somethingChanged){
            return $sql;
        }
        else{
            $language = Languages::getInstance();
            $this->throwDataAccessException($language->get('nothing_changed'));
        }
    }
    
    private function valuesAreDifferent(string $key, Container $old, Container $new){
        return $old->get($key) !== $new->get($key);
    }
    
    private function add(Container $item) : int {
        $sql = new MysqlBuilder();
        $sql->insert($this->table);
        $keys = $item->getKeys();
        foreach ($keys as $key){
            $sql->into($key, $item->get($key));
        }
        return $this->insert($sql);
    }
    
    public function remove(int $id){
        $sql = new MysqlBuilder();
        $sql->delete($this->table, 'id', $id);
        $this->delete($sql);
    }
    
    protected function select(SqlBuilder $sql) : Container {
        $logger = Logger::getInstance();
        $msg = 'Database: '  . $this->dbIndex . '; SQL: ' . $sql->formSQL();
        $logger->logSelect($msg);
        $database = Database::getInstance($this->dbIndex);
        $data = $database->select($sql);
        return $data;
    }
    
    protected function insert(SqlBuilder $sql) : int {
        $logger = Logger::getInstance();
        $msg = 'Database: '  . $this->dbIndex . '; SQL: ' . $sql->formSQL();
        $logger->logInsert($msg);
        $database = Database::getInstance($this->dbIndex);
        $id = $database->insert($sql);
        return $id;
    }
    
    protected function update(SqlBuilder $sql) : void {
        $logger = Logger::getInstance();
        $msg = 'Database: '  . $this->dbIndex . '; SQL: ' . $sql->formSQL();
        $logger->logUpdate($msg);
        $database = Database::getInstance($this->dbIndex);
        $database->update($sql);
    }
    
    protected function delete(SqlBuilder $sql) : void {
        $logger = Logger::getInstance();
        $msg = 'Database: '  . $this->dbIndex . '; SQL: ' . $sql->formSQL();
        $logger->logInsert($msg);
        $database = Database::getInstance($this->dbIndex);
        $database->delete($sql);
    }
    
    protected function throwDataAccessException(string $msg){
        throw new DataAccessException($msg);
    }
    
    protected function throwNotFoundException(string $name){
        $languages = Languages::getInstance();
        throw new NotFoundException($name . ' ' . $languages->get('not_found'));
    }
    
    protected function throwIdColumnException(string $table){
        throw new IdColumnException($table . ' id column is not unique.');
    }
}

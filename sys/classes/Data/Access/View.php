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
 * Description of View
 *
 * @author Tanzar
 */
abstract class View {
    private string $dbIndex;
    private string $name;
    
    protected function __construct() {
        $dbIndex = $this->setDatabaseIndex();
        $this->dbIndex = $dbIndex;
        $this->name = $this->setDefaultName();
    }
    
    protected abstract function setDatabaseIndex() : string;
    
    protected abstract function setDefaultName() : string;
    
    public function getAll() : Container {
        $sql = new MysqlBuilder();
        $sql->select($this->name);
        return $this->select($sql);
    }
    
    protected function select(SqlBuilder $sql) : Container {
        $logger = Logger::getInstance();
        $msg = 'Database: '  . $this->dbIndex . '; SQL: ' . $sql->formSQL();
        $logger->logSelect($msg);
        $database = Database::getInstance($this->dbIndex);
        $data = $database->select($sql);
        return $data;
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

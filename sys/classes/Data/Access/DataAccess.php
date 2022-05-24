<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Tanweb\Database\SQL\SqlBuilder as SqlBuilder;
use Tanweb\Database\Database as Database;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Container as Container;

/**
 * Description of DataObject
 *
 * @author Tanzar
 */
abstract class DataAccess {
    private string $dbIndex;
    
    public function __construct() {
        $dbIndex = $this->setDatabaseIndex();
        $this->dbIndex = $dbIndex;
    }
    
    protected abstract function setDatabaseIndex() : string;
    
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
}

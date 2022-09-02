<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DecisionLawDAO
 *
 * @author Tanzar
 */
class DecisionLawDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'decision_law';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_law')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getActiveRequiringSuspension() : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_law')->where('active', 1)
                ->and()->where('requires_suspension', 1);
        return $this->select($sql);
    }
    
    public function getActiveNotRequiringSuspension() : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_law')->where('active', 1)
                ->and()->where('requires_suspension', 0);
        return $this->select($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('decision_law', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('decision_law', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

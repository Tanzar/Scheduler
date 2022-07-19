<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of PositionGroupsDAO
 *
 * @author Tanzar
 */
class PositionGroupsDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'position_groups';
    }

    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('position_groups')->where('active', 1);
        return $this->select($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('position_groups', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function  disable(int $id) {
        $sql = new MysqlBuilder();
        $sql->update('position_groups', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
}

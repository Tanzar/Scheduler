<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of SupervisionLevelDAO
 *
 * @author Tanzar
 */
class SupervisionLevelDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'supervision_level';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('supervision_level')->where('active', 1);
        return $this->select($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('supervision_level', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('supervision_level', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

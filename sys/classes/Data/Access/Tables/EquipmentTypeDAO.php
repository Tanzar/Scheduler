<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of EquipmentTypeDAO
 *
 * @author Tanzar
 */
class EquipmentTypeDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'equipment_type';
    }

    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_type')->where('active', 1);
        return $this->select($sql);
    }
    
    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment_type', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment_type', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
}

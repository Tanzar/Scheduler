<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of EquipmentDAO
 *
 * @author Tanzar
 */
class EquipmentDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'equipment';
    }

    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment')->where('active', 1);
        return $this->select($sql);
    }
    
    public function changeUser(int $id, int $userId) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)->set('id_user', $userId);
        $this->update($sql);
    }
    
    public function setAsOnList(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)
                ->set('active', 1)
                ->set('state', 'list');
        $this->update($sql);
    }
    
    public function setAsRepair(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)->set('state', 'repair');
        $this->update($sql);
    }
    
    public function setAsCalibration(int $id, string $date) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)
                ->set('state', 'calibration')
                ->set('calibration', $date);
        $this->update($sql);
    }
    
    public function setAsLiquidation(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)
                ->set('active', 0)
                ->set('state', 'liquidation');
        $this->update($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)
                ->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('equipment', 'id', $id)
                ->set('active', 1);
        $this->update($sql);
    }
}

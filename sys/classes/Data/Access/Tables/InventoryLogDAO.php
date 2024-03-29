<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of InventoryLogDAO
 *
 * @author Tanzar
 */
class InventoryLogDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'inventory_log';
    }
    
    public function newEquipment(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $confirmation = 0;
        if($userTargetId === 1){
            $confirmation = 1;
        }
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'new')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', $confirmation);
        return $this->insert($sql);
    }

    public function assign(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $confirmation = 0;
        if($userTargetId === 1){
            $confirmation = 1;
        }
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'assign')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', $confirmation);
        return $this->insert($sql);
    }
    
    public function borrowed(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $confirmation = 0;
        if($userTargetId === 1){
            $confirmation = 1;
        }
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'borrowed')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', $confirmation);
        return $this->insert($sql);
    }
    
    public function returned(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'returned')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', 1);
        return $this->insert($sql);
    }
    
    public function confirm(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('inventory_log', 'id', $id)->set('confirmation', 1);
        $this->update($sql);
    }
    
    public function cancelAssign(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'cancel_assign')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', 1);
        return $this->insert($sql);
    }
    
    
    public function repair(string $date, string $document, int $equipmentId, int $userId) : int {
        $remarks = '' . $date . ': ' . $document;
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'repair')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userId)
                ->into('id_user_target', $userId)
                ->into('remarks', $remarks)
                ->into('confirmation', 1);
        return $this->insert($sql);
    }
    
    public function calibration(string $date, string $document, int $equipmentId, int $userId) : int {
        $remarks = '' . $date . ': ' . $document;
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'calibration')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userId)
                ->into('id_user_target', $userId)
                ->into('remarks', $remarks)
                ->into('confirmation', 1);
        return $this->insert($sql);
    }
    
    public function liquidation(string $date, string $document, int $equipmentId, int $userId) : int {
        $this->confirmAssigns($equipmentId);
        $remarks = '' . $date . ': ' . $document;
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'liquidation')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userId)
                ->into('id_user_target', $userId)
                ->into('remarks', $remarks)
                ->into('confirmation', 1);
        return $this->insert($sql);
    }
    
    public function confirmAssigns(int $equipmentId) : void {
        $sql = new MysqlBuilder();
        $sql->select('inventory_log')->where('id_equipment', $equipmentId)
                ->and()->where('confirmation', 0);
        $logs = $this->select($sql);
        foreach ($logs->toArray() as $item){
            $id = (int) $item['id'];
            $this->confirm($id);
        }
    }
}

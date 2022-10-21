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
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'new')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', 0);
        return $this->insert($sql);
    }

    public function assign(int $equipmentId, int $userSourceId, int $userTargetId) : int {
        $sql = new MysqlBuilder();
        $sql->insert('inventory_log')->into('operation', 'assign')
                ->into('id_equipment', $equipmentId)
                ->into('id_user_source', $userSourceId)
                ->into('id_user_target', $userTargetId)
                ->into('remarks', '')
                ->into('confirmation', 0);
        return $this->insert($sql);
    }
    
    public function confirm(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('inventory_log', 'id', $id)->set('confirmation', 1);
        $this->update($sql);
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
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;
use Tanweb\Database\DataFilter\DataFilter as DataFilter;

/**
 * Description of EquipmentLogDetailsView
 *
 * @author Tanzar
 */
class InventoryLogDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'inventory_log_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('inventory_log_details')->where('id', $id);
        $logs = $this->select($sql);
        if($logs->length() > 1){
            $this->throwIdColumnException('inventory_log_details');
        }
        if($logs->isEmpty()){
            return new Container();
        }
        else{
            return new Container($logs->get(0));
        }
    }
    
    public function getUnconfirmedForEquipment(int $equipmentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('inventory_log_details')->where('confirmation', 0)
                ->and()->where('id_equipment', $equipmentId);
        return $this->select($sql);
    }
    
    public function getUnconfirmedForUser(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('inventory_log_details')->where('confirmation', 0)
                ->and()->where('target_username', $username);
        return $this->select($sql);
    }
    
    public function getByDate(string $date) : Container {
        $sql = new MysqlBuilder();
        $sql->select('inventory_log_details')->where('save_time_date', $date);
        return $this->select($sql);
    }
    
    public function getFiltered(DataFilter $filter) : Container {
        $sql = $filter->generateSQL();
        return $this->select($sql);
    }
}

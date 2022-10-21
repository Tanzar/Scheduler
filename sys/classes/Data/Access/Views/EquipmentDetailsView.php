<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of EquipmentDetails
 *
 * @author Tanzar
 */
class EquipmentDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'equipment_details';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getActiveMeasurementInstruments() : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('measurement_instrument', 1);
        return $this->select($sql);
    }
    
    public function getActiveByState(string $state) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('state', $state);
        return $this->select($sql);
    }
    
    public function getByState(string $state) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('state', $state);
        return $this->select($sql);
    }
    
    public function getActiveByType(int $idType) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('id_equipment_type', $idType);
        return $this->select($sql);
    }
    
    public function getActiveByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('username', $username);
        return $this->select($sql);
    }
    
    public function getActiveByInventoryNumber(string $number) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('inventory_number', '%' . $number . '%', 'like');
        return $this->select($sql);
    }
    
    public function getActiveByRemarks(string $text) : Container {
        $sql = new MysqlBuilder();
        $sql->select('equipment_details')->where('active', 1)
                ->and()->where('remarks', '%' . $text . '%', 'like');
        return $this->select($sql);
    }
}

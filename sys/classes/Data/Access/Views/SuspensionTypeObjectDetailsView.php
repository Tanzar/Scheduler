<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of SuspensionTypeObjectDetailsView
 *
 * @author Tanzar
 */
class SuspensionTypeObjectDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'suspension_type_object_details';
    }
    
    public function getActiveObjectsByType(int $idType) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_object_details')
                ->where('suspension_object_active', 1)
                ->and()->where('id_suspension_type', $idType);
        return $this->select($sql);
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_object_details')
                ->where('suspension_object_active', 1)
                ->and()->where('suspension_type_active', 1)
                ->and()->where('suspension_group_active', 1);
        return $this->select($sql);
    }
}

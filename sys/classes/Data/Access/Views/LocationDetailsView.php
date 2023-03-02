<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of LocationDetailsDAO
 *
 * @author Tanzar
 */
class LocationDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'location_details';
    }
    
    public function getByIdLocationGroup(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('id_location_group', $id);
        return $this->select($sql);
    }
    
    public function getInspectable() : Container {
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('inspection', 1);
        return $this->select($sql);
    }
    
    public function getInTemporaryGroup() : Container {
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('location_group', 'tmp');
        return $this->select($sql);
    }
}

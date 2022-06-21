<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of ActivityLocationType
 *
 * @author Tanzar
 */
class ActivityLocationTypeDAO extends DAO{
    
    public function __construct() {
        parent::__construct(false);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'activity_location_type';
    }

    public function enable(int $id) : void{
        $sql = new MysqlBuilder();
        $sql->update('activity_location_type', 'id', $id)
                ->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('activity_location_type', 'id', $id)
                ->set('active', 0);
        $this->update($sql);
    }

}

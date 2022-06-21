<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;

/**
 * Description of ActivityLocationTypeDetailsDAO
 *
 * @author Tanzar
 */
class ActivityLocationTypeDetailsDAO extends DAO{
    
    public function __construct() {
        parent::__construct(true);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'activity_location_type_details';
    }
    
    public function getByActivityId(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('activity_location_type_details')->where('active', 1)
                ->and()->where('id_activity', $id);
        return $this->select($sql);
    }

}

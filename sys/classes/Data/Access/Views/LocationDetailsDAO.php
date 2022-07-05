<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;

/**
 * Description of LocationDetailsDAO
 *
 * @author Tanzar
 */
class LocationDetailsDAO extends DAO{
    
    public function __construct() {
        parent::__construct(true);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'location_details';
    }
    
    public function getByIdLocationGroup(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('id_location_group', $id);
        return $this->select($sql);
    }
}

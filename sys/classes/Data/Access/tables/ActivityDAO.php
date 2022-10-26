<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of ActivityAccess
 *
 * @author Tanzar
 */
class ActivityDataAccess extends DataAccess{
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    public function getActivities() : Container {
        $sql = new MysqlBuilder();
        $sql->select('activity');
        $data = $this->select($sql);
        return $data;
    }
    
    public function getActivityGroups() : Container{
        $sql = new MysqlBuilder();
        $sql->select('actitvity_group');
        return $this->select($sql);
    }
    
    public function newActivity(Container $data) : int {
        $sql = new MysqlBuilder();
        $sql->insert('activity')
                ->into('name', $data->getValue('name'))
                ->into('short', $data->getValue('short'))
                ->into('color', $data->getValue('color'))
                ->into('id_activity_group', $data->getValue('id_activity_group'));
        return $this->insert($sql);
    }
    
    public function newActivityGroup(Container $data) : int {
        $sql = new MysqlBuilder();
        $sql->insert('activity_group')->into('name', $data->getValue('name'));
        return $this->insert($sql);
    }

}

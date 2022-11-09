<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of ActivityTableDAO
 *
 * @author Tanzar
 */
class ActivityTableDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'activity';
    }

    public function getActivities() : Container {
        $sql = new MysqlBuilder();
        $sql->select('activity');
        $data = $this->select($sql);
        return $data;
    }
    
    public function getByGroup(string $group) : Container{
        $sql = new MysqlBuilder();
        $sql->select('activity')->where('activity_group', $group);
        return $this->select($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('activity', 'id', $id)
                ->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('activity', 'id', $id)
                ->set('active', 0);
        $this->update($sql);
    }
}

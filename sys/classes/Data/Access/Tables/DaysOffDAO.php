<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DaysOffDAO
 *
 * @author Tanzar
 */
class DaysOffDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'days_off';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getActiveNotForAll() : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off')->where('active', 1)
                ->and()->where('for_all', 0);
        return $this->select($sql);
    }
    
    public function getActiveForAllByMonthAndYear(int $month, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('days_off')->where('active', 1)
                ->and()->where('for_all', 1)
                ->and()->where('month(date)', $month)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('days_off', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('days_off', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

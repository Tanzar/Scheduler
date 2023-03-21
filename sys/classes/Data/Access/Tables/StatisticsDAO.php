<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;
use Custom\Statistics\Options\Type as Type;

/**
 * Description of StatisticsDAO
 *
 * @author Tanzar
 */
class StatisticsDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'statistics';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('statistics')->where('active', 1)->orderBy('sort_priority');
        return $this->select($sql);
    }
    
}

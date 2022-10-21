<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of InstrumentUsageDAO
 *
 * @author Tanzar
 */
class InstrumentUsageDAO  extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'instrument_usage';
    }

    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('instrument_usage', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('instrument_usage', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
}

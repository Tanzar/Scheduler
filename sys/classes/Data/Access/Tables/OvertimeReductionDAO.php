<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of OvertimeReductionDAO
 *
 * @author Tanzar
 */
class OvertimeReductionDAO  extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'overtime_reduction';
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('overtime_reduction', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('overtime_reduction', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of DocumentScheduleDAO
 *
 * @author Tanzar
 */
class DocumentScheduleDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'document_schedule';
    }

    public function getByScheduleId(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('document_schedule')->where('id_schedule', $id);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwDataAccessException('column id_schedule must contain '
                    . 'unique values, cannot assign single entry multiple times');
        }
        return new Container($result->get(0));
    }
    
    public function disable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('document_schedule', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('document_schedule', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of TicketLawDAO
 *
 * @author Tanzar
 */
class TicketLawDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'ticket_law';
    }
    
    public function getActive() : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_law')->where('active', 1);
        return $this->select($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('ticket_law', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
    
    public function  disable(int $id) {
        $sql = new MysqlBuilder();
        $sql->update('ticket_law', 'id', $id)->set('active', 0);
        $this->update($sql);
    }

}

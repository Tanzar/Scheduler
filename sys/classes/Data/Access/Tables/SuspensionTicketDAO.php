<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of SuspensionTicketDAO
 *
 * @author Tanzar
 */
class SuspensionTicketDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'suspension_ticket';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_ticket')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getBySuspensionAndTicket(int $idSuspension, int $idTicket) : Container {
        $sql = new MysqlBuilder();
        $sql->select('suspension_ticket')->where('id_suspension', $idSuspension)
                ->and()->where('id_ticket', $idTicket);
        $result = $this->select($sql);
        if($result->length() > 1){
            $this->throwDataAccessException('combinations for columns '
                    . 'id_suspension (' . $idSuspension . ') and id_ticket (' 
                    . $idTicket . ') must be unique, duplicate found.');
        }
        if($result->length() === 0){
            $this->throwNotFoundException('relation not found');
        }
        else{
            return new Container($result->get(0));
        }
    }
}

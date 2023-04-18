<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of TicketDetailsView
 *
 * @author Tanzar
 */
class TicketDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'ticket_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('id', $id);
        $tickets = $this->select($sql);
        if($tickets->length() > 1){
            $this->throwIdColumnException('document');
        }
        if($tickets->isEmpty()){
            return new Container();
        }
        else{
            return new Container($tickets->get(0));
        }
    }
    
    public function getAllUserTicketsByDocumentId(string $username, int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('id_document', $documentId)
                ->and()->where('username', $username);
        return $this->select($sql);
    }
    
    public function getAllUserTicketsByYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('year(date)', $year)
                ->and()->where('username', $username)->orderBy('date', false);
        return $this->select($sql);
    }
    
    public function getActiveUserTicketsByYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('active', 1)->and()
                ->where('year(date)', $year)->and()
                ->where('username', $username)->orderBy('date', false);
        return $this->select($sql);
    }
    
    public function getUserActiveTicketsByDocumentId(string $username, int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('active', 1)
                ->and()->where('id_document', $documentId)
                ->and()->where('username', $username);
        return $this->select($sql);
    }
    
    public function getUserActiveTicketsByMonthAndYear(string $username, int $month, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('month(date)', $month)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByMonthAndYear(int $month, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('active', 1)
                ->and()->where('month(date)', $month)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('ticket_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}

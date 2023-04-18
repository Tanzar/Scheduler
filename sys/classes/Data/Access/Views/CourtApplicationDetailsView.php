<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of CourtApplicationDetailsView
 *
 * @author Tanzar
 */
class CourtApplicationDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'court_application_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('id', $id);
        $applications = $this->select($sql);
        if($applications->length() > 1){
            $this->throwIdColumnException('court_application');
        }
        if($applications->isEmpty()){
            return new Container();
        }
        else{
            return new Container($applications->get(0));
        }
    }
    
    public function getActiveByUsernameAndDocument(string $username, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
    
    public function getAllByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getUserActiveByYear(string $username, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByMonthAndYear(int $month, int $year) : Container{
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('active', 1)
                ->and()->where('month(date)', $month)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('court_application_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}

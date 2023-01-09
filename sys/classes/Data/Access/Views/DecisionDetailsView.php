<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of DecisionDetailsView
 *
 * @author Tanzar
 */
class DecisionDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'decision_details';
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('id', $id);
        $decisions = $this->select($sql);
        if($decisions->length() > 1){
            $this->throwIdColumnException('document');
        }
        if($decisions->isEmpty()){
            return new Container();
        }
        else{
            return new Container($decisions->get(0));
        }
    }
    
    public function getAllByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getAllRequiringSuspension() : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('requires_suspension', 1);
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAndYear(string $username, int $year) : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('year(date)', $year);
        return $this->select($sql);
    }
    
    public function getActiveByUsernameAndDocumentId(string $username, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
    
    public function getActiveRequiringSuspensionByUsernameAndDocumentId(string $username, int $documentId) : Container {
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('active', 1)
                ->and()->where('username', $username)
                ->and()->where('id_document', $documentId)
                ->and()->where('requires_suspension', 1);
        return $this->select($sql);
    }
    
    public function getActiveByDocumentId(int $documentId) : Container{
        $sql = new MysqlBuilder();
        $sql->select('decision_details')->where('active', 1)
                ->and()->where('id_document', $documentId);
        return $this->select($sql);
    }
}

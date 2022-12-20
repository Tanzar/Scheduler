<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Data\Access\View as View;
use Tanweb\Container as Container;

/**
 * Description of QualificationsDetailsView
 *
 * @author Tanzar
 */
class QualificationsDetailsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultName(): string {
        return 'qualifications_details';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('qualifications_details')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getAllByIdPerson(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('qualifications_details')->where('id_person', $id);
        return $this->select($sql);
    }
    
    public function getActiveByIdPerson(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('qualifications_details')->where('active', 1)->and()->where('id_person', $id);
        return $this->select($sql);
    }
    
    public function getById(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('qualifications_details')->where('id', $id);
        $applications = $this->select($sql);
        if($applications->length() > 1){
            $this->throwIdColumnException('qualifications');
        }
        if($applications->isEmpty()){
            return new Container();
        }
        else{
            return new Container($applications->get(0));
        }
    }
    
}

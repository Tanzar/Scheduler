<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of EducationDAO
 *
 * @author Tanzar
 */
class EducationDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'education';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('education')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getAllByIdPerson(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('education')->where('id_person', $id);
        return $this->select($sql);
    }
    
    public function getActiveByIdPerson(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('education')->where('active', 1)->and()->where('id_person', $id);
        return $this->select($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('education', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('education', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

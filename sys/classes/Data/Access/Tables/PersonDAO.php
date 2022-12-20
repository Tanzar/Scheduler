<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of PersonDAO
 *
 * @author Tanzar
 */
class PersonDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'person';
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('person')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getAllByNameAndSurname(string $name, string $surname) : Container {
        $sql = new MysqlBuilder();
        $sql->select('person');
        if(!empty($name)){
            $sql->where('name', $name, 'like');
        }
        if(!empty($surname)){
            if(empty($name)){
                $sql->where('surname', $surname, 'like');
            }
            else{
                $sql->and()->where('surname', $surname, 'like');
            }
        }
        return $this->select($sql);
    }
    
    public function getActiveByNameAndSurname(string $name, string $surname) : Container {
        $sql = new MysqlBuilder();
        $sql->select('person')->where('active', 1);
        if(!empty($name)){
            $sql->and()->where('name', $name, 'like');
        }
        if(!empty($surname)){
            $sql->and()->where('surname', $surname, 'like');
        }
        return $this->select($sql);
    }
    
    public function disable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('person', 'id', $id)->set('active', 0);
        $this->update($sql);
    }
    
    public function enable(int $id){
        $sql = new MysqlBuilder();
        $sql->update('person', 'id', $id)->set('active', 1);
        $this->update($sql);
    }
}

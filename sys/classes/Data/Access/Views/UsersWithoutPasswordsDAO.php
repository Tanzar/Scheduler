<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of UsersWithoutPasswordsDAO
 *
 * @author Tanzar
 */
class UsersWithoutPasswordsDAO extends DAO{
    
    public function __construct() {
        parent::__construct(false);
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultTable(): string {
        return 'users_without_passwords';
    }
    
    public function getActive(){
        $sql = new MysqlBuilder();
        $sql->select('users_without_passwords')->where('active', 1);
        return $this->select($sql);
    }
    
    public function findUsers(Container $conditions) : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_without_passwords');
        $first = true;
        
        foreach ($conditions->toArray() as $column => $value){
            $first = !$this->addCondition($sql, $first, $column, $value);
        }
        
        $data = $this->select($sql);
        return $data;
    }
    
    private function addCondition(MysqlBuilder $sql, bool $first, $column, $value){
        if($value !== ''){
            if(!$first){
                $sql->and();
            }
            if($column === 'active'){
                $sql->where('active', $value);
                return true;
            }
            else{
                $sql->where($column, $value, 'like');
                return true;
            }
        }
        return false;
    }

}

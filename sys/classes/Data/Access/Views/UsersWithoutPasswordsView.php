<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Views;

use Data\Access\View as View;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of UsersWithoutPasswordsDAO
 *
 * @author Tanzar
 */
class UsersWithoutPasswordsView extends View{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    protected function setDefaultName(): string {
        return 'users_without_passwords';
    }
    
    public function getActive() : Container{
        $sql = new MysqlBuilder();
        $sql->select('users_without_passwords')->where('active', 1);
        return $this->select($sql);
    }
    
    public function getByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('users_without_passwords')->where('username', $username);
        $users = $this->select($sql);
        if($users->length() > 1){
            $this->throwDataAccessException('username column is nor unique');
        }
        if($users->length() === 1){
            return new Container($users->get(0));
        }
        else{
            return new Container();
        }
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

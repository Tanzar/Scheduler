<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Data\Exceptions\UserDataException as UserDataException;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Security\Encrypter as Encrypter;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of UserDataAccess
 *
 * @author Tanzar
 */
class UserDataAccess extends DataAccess{
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    public function getAllUsers(bool $withPasswords = false) : Container{
        $sql = new MysqlBuilder();
        if($withPasswords){
            $sql->select('user');
        }
        else{
            $sql->select('users_without_passwords');
        }
        $data = $this->select($sql);
        return $data;
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

    public function getUserByID(int $id) : Container {
        $sql = new MysqlBuilder();
        $sql->select('user')->where('id', $id);
        $data = $this->select($sql);
        if($data->getLength() > 1){
            throw new UserDataException("id column don't hold unique values, "
                    . 'multiple ids found.');
        }
        if($data->getLength() === 0){
            $languages = Languages::getInstance();
            throw new UserDataException($languages->get('not_found'));
        }
        $user = $data->getValue(0);
        return new Container($user);
    }
    
    public function getUserByUsername(string $username) : Container {
        $sql = new MysqlBuilder();
        $sql->select('user')->where('username', $username);
        $data = $this->select($sql);
        if($data->getLength() > 1){
            throw new UserDataException("username column don't hold unique values, "
                    . 'multiple usernamess found.');
        }
        if($data->getLength() === 0){
            $languages = Languages::getInstance();
            throw new UserDataException($languages->get('not_found'));
        }
        $user = $data->getValue(0);
        return new Container($user);
    }
    
    public function create(Container $user) : int {
        if($this->isUsernameTaken($user->getValue('username'))){
            $languages = Languages::getInstance();
            throw new UserDataException($languages->get('username_taken'));
        }
        $sql = new MysqlBuilder();
        $sql->insert('user');
        $sql->into('username', $user->getValue('username'));
        $sql->into('name', $user->getValue('name'));
        $sql->into('surname', $user->getValue('surname'));
        $uncodedPassword = $user->getgetValue('password');
        $encodedPassword = Encrypter::encode($uncodedPassword);
        $sql->into('password', $encodedPassword);
        $id = $this->insert($sql);
        return $id;
    }
    
    public function updateUser(Container $user) : void{
        if($this->isUsernameTaken($user->getValue('username'))){
            $languages = Languages::getInstance();
            throw new UserDataException($languages->get('username_taken'));
        }
        $id = $user->getId();
        $sql = new MysqlBuilder();
        $sql->update('user', 'id', $id);
        $sql->set('name', $user->getValue('name'));
        $sql->set('surname', $user->getValue('surname'));
        $sql->set('username', $user->getValue('username'));
        $this->update($sql);
    }
    
    public function changePassword(string $username, string $password) : void{
        $user = $this->getUserByUsername($username);
        $encoded = Encrypter::encode($password);
        $id = $user->getValue('id');
        $sql = new MysqlBuilder();
        $sql->update('user', 'id', $id);
        $sql->set('password', $encoded);
        $this->update($sql);
    }
    
    public function deactivate(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('user', 'id', $id);
        $sql->set('active', 0);
        $this->update($sql);
    }
    
    public function activate(int $id) : void {
        $sql = new MysqlBuilder();
        $sql->update('user', 'id', $id);
        $sql->set('active', 1);
        $this->update($sql);
    }
    
    public function isUsernameTaken(string $username) : bool {
        $sql = new MysqlBuilder();
        $sql->select('user')->where('username', $username);
        $result = $this->select($sql);
        if($result->getLength() >= 1){
            return true;
        }
        return false;
    }
    
}

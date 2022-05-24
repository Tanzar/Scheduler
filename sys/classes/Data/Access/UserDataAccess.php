<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access;

use Data\Access\DataAccess as DataAccess;
use Data\Entities\User as User;
use Data\Containers\Users as Users;
use Data\Exceptions\UserDataException as UserDataException;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Security\Encrypter as Encrypter;
use Tanweb\Container as Container;

/**
 * Description of UserDataAccess
 *
 * @author Tanzar
 */
class UserDataAccess extends DataAccess{
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }
    
    public function getAllUsers(bool $withPasswords = false) : Users{
        $sql = new MysqlBuilder();
        if($withPasswords){
            $sql->select('user');
        }
        else{
            $sql->select('users_without_passwords');
        }
        $data = $this->select($sql);
        return $this->parseUsers($data);
    }
    
    public function findUsers(Container $conditions) : Users{
        $sql = new MysqlBuilder();
        $sql->select('users_without_passwords');
        $first = true;
        
        foreach ($conditions->toArray() as $column => $value){
            $first = !$this->addCondition($sql, $first, $column, $value);
        }
        
        $data = $this->select($sql);
        return $this->parseUsers($data);
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

    public function getUserByID(int $id) : User {
        $sql = new MysqlBuilder();
        $sql->select('user')->where('id', $id);
        $data = $this->select($sql);
        if($data->getLength() > 1){
            throw new UserDataException("id column don't hold unique values, "
                    . 'multiple ids found.');
        }
        if($data->getLength() === 0){
            throw new UserDataException('user not found.');
        }
        $user = $data->getValue(0);
        return new User($user);
    }
    
    public function getUserByUsername(string $username) : User {
        $sql = new MysqlBuilder();
        $sql->select('user')->where('username', $username);
        $data = $this->select($sql);
        if($data->getLength() > 1){
            throw new UserDataException("username column don't hold unique values, "
                    . 'multiple usernamess found.');
        }
        if($data->getLength() === 0){
            throw new UserDataException('user not found.');
        }
        $user = $data->getValue(0);
        return new User($user);
    }
    
    public function create(User $user) : int {
        if($this->isUsernameTaken($user->getUsername())){
            throw new UserDataException('Cannot add user, username taken.');
        }
        $sql = new MysqlBuilder();
        $sql->insert('user');
        $sql->into('username', $user->getUsername());
        $sql->into('name', $user->getName());
        $sql->into('surname', $user->getSurname());
        $uncodedPassword = $user->getPassword();
        $encodedPassword = Encrypter::encode($uncodedPassword);
        $sql->into('password', $encodedPassword);
        $id = $this->insert($sql);
        return $id;
    }
    
    public function updateUser(User $user) : void{
        if($this->isUsernameTaken($user->getUsername())){
            throw new UserDataException('Cannot update, username taken.');
        }
        $id = $user->getId();
        $sql = new MysqlBuilder();
        $sql->update('user', 'id', $id);
        $sql->set('name', $user->getName());
        $sql->set('surname', $user->getSurname());
        $sql->set('username', $user->getUsername());
        $this->update($sql);
    }
    
    public function changePassword(string $username, string $password) : void{
        $user = $this->getUserByUsername($username);
        $encoded = Encrypter::encode($password);
        $id = $user->getId();
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
    
    private function parseUsers(Container $data) : Users{
        $users = new Users();
        foreach ($data->toArray() as $item){
            $user = new User($item);
            $users->add($user);
        }
        return $users;
    }
}

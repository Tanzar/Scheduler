<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Tanweb\Container as Container;
use Data\Access\UserDataAccess as UserDataAccess;
use Data\Containers\Users as Users;
use Data\Entities\User as User;

/**
 * Description of UserService
 *
 * @author Tanzar
 */
class UserService{
    private UserDataAccess $userDataAccess;
    
    public function __construct() {
        $this->userDataAccess = new UserDataAccess();
    }
    
    public function getAll() : Container {
        $users = $this->userDataAccess->getAllUsers();
        return $this->parseUsers($users);
    }
    
    public function findUsers(Container $conditions) : Container{
        $users = $this->userDataAccess->findUsers($conditions);
        return $this->parseUsers($users);
    }
    
    public function addUser(Container $data) : int{
        $user = new User($data->toArray());
        $id = $this->userDataAccess->create($user);
        return $id;
    }
    
    public function updateUser(Container $data){
        $user = new User($data->toArray());
        $this->userDataAccess->updateUser($user);
    }
    
    public function changePassword(Container $data){
        $username = $data->getValue('username');
        $password = $data->getValue('password');
        $this->userDataAccess->changePassword($username, $password);
    }
    
    public function changeStatus(Container $data){
        if($data->isValueSet('id')){
            $id = $data->getValue('id');
            $user = $this->userDataAccess->getUserByID($id);
            
        }
        elseif($data->isValueSet('username')) {
            $username = $data->getValue('username');
            $user = $this->userDataAccess->getUserByUsername($username);
        }
        $active = $user->getActive();
        if($active){
            $this->userDataAccess->deactivate($user->getId());
        }
        else{
            $this->userDataAccess->activate($user->getId());
        }
    }
    
    private function parseUsers(Users $users) : Container {
        $result = new Container();
        foreach ($users->toArray() as $user){
            $item  = $user->toArray();
            $result->add($item);
        }
        return $result;
    }
}

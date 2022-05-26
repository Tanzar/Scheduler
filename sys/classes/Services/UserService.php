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
        return $this->userDataAccess->getAllUsers();
    }
    
    public function getByUsername(string $username) : Container {
        return $this->userDataAccess->getUserByUsername($username);
    }
    
    public function findUsers(Container $conditions) : Container{
        return $this->userDataAccess->findUsers($conditions);
    }
    
    public function addUser(Container $data) : int{
        $id = $this->userDataAccess->create($data);
        return $id;
    }
    
    public function updateUser(Container $data){
        $this->userDataAccess->updateUser($data);
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
        $active = $user->getValue('active');
        if($active){
            $this->userDataAccess->deactivate($user->getValue('id'));
        }
        else{
            $this->userDataAccess->activate($user->getValue('id'));
        }
    }
}

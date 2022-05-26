<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\UserService as UserService;
use Services\PrivilagesService as PrivilagesService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminController
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class AdminPanelUsers extends Controller{
    private UserService $userService;
    private PrivilagesService $privilagesService;
    
    public function __construct() {
        $this->userService = new UserService();
        $this->privilagesService = new PrivilagesService();
        $privilages = new Container(['admin']);
        parent::__construct($privilages);
    }
    
    public function getAllUsers() {
        $result = $this->userService->getAll();
        $this->setResponse($result);
    }
    
    public function getPrivilagesList(){
        $result = $this->privilagesService->getConfigPrivilages();
        $this->setResponse($result);
    }
    
    public function getUserPrivilages(){
        $data = $this->getRequestData();
        $id = $data->getValue('id_user');
        $result = $this->privilagesService->getUserPrivilages($id);
        $this->setResponse($result);
    }
    
    public function findUsers(){
        $conditions = $this->getRequestData();
        $data = $this->userService->findUsers($conditions);
        $this->setResponse($data);
    }
    
    public function addPrivilage(){
        $data = $this->getRequestData();
        $this->privilagesService->addPrivilage($data);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('privilage_added'), 'message');
        $this->setResponse($response);
    }
    
    public function saveUser(){
        $data = $this->getRequestData();
        if($data->isValueSet('id')){
            $this->userService->updateUser($data);
        }
        else{
            $this->userService->addUser($data);
        }
    }
    
    public function changeUserPassword(){
        $data = $this->getRequestData();
        $this->userService->changePassword($data);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('password_change'), 'message');
        $this->setResponse($response);
    }
    
    public function changeUserStatus(){
        $data = $this->getRequestData();
        $this->userService->changeStatus($data);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('status_change'), 'message');
        $this->setResponse($response);
    }
    
    public function changePrivilageStatus(){
        $data = $this->getRequestData();
        $id = $data->getValue('id');
        $this->privilagesService->changeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('status_change'), 'message');
        $this->setResponse($response);
    }
}

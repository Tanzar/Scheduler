<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\UserService as UserService;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminController
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class AdminPanelUsers extends Controller{
    private UserService $userService;
    
    public function __construct() {
        $this->userService = new UserService();
        $privilages = new Container(['admin']);
        parent::__construct($privilages);
    }
    
    public function getUserTypes() : void {
        $response = $this->userService->getUserTypes();
        $this->setResponse($response);
    }
    
    public function getAllUsers() {
        $result = $this->userService->getAllUsers();
        $this->setResponse($result);
    }
    
    public function getPrivilagesList(){
        $appconfig = AppConfig::getInstance();
        $privilages = $appconfig->getSecurity();
        $arr = $privilages->get('privilages');
        $result = new Container($arr);
        $this->setResponse($result);
    }
    
    public function getUserPrivilages(){
        $data = $this->getRequestData();
        $id = $data->get('id_user');
        $result = $this->userService->getUserPrivilages($id);
        $this->setResponse($result);
    }
    
    public function getUserEmploymentPeriods(){
        $data = $this->getRequestData();
        $id = $data->get('id_user');
        $result = $this->userService->getUserEmploymentPeriods($id);
        $this->setResponse($result);
    }
    
    public function findUsers(){
        $conditions = $this->getRequestData();
        $data = $this->userService->findUsers($conditions);
        $this->setResponse($data);
    }
    
    public function savePrivilage(){
        $data = $this->getRequestData();
        $id = $this->userService->savePrivilage($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveEmploymentPeriod(){
        $data = $this->getRequestData();
        $id = $this->userService->saveEmploymentPeriod($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveUser(){
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->userService->saveUser($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
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
        $this->userService->changeUserStatus($data);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changePrivilageStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->userService->changePrivilageStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeEmploymentPeriodStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->userService->changeEmploymentStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

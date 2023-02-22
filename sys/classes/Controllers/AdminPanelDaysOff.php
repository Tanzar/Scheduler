<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\DaysOffService as DaysOffService;
use Services\UserService as UserService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelDaysOff
 *
 * @author Tanzar
 */
class AdminPanelDaysOff extends Controller{
    private DaysOffService $daysOffService;
    private UserService $userService;
    
    public function __construct() {
        $this->daysOffService = new DaysOffService();
        $this->userService = new UserService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getDaysOff() {
        $response = $this->daysOffService->getALL();
        $this->setResponse($response);
    }
    
    public function getUsers() {
        $data = $this->getRequestData();
        $dayOffId = (int) $data->get('id_days_off');
        $response = $this->daysOffService->getUsersByDay($dayOffId);
        $this->setResponse($response);
    }
    
    public function getSpecialWorkdays() {
        $response = $this->daysOffService->getSpecialWorkdays();
        $this->setResponse($response);
    }
    
    public function getMatchingUsers() {
        $data = $this->getRequestData();
        $date = $data->get('date');
        $response = $this->userService->getEmployedUsersListOrdered($date);
        $this->setResponse($response);
    }
    
    public function saveDayOff() {
        $data = $this->getRequestData();
        $id = $this->daysOffService->saveDayOff($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveUserForDay() {
        $data = $this->getRequestData();
        $username = $data->get('username');
        $dayOffId = (int) $data->get('id_days_off');
        $id = $this->daysOffService->saveDayForUser($username, $dayOffId);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    
    public function saveSpecialWorkday() {
        $data = $this->getRequestData();
        $id = $this->daysOffService->saveSpecialWorkday($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    
    public function removeUserFromDay() {
        $data = $this->getRequestData();
        $username = $data->get('username');
        $dayOffId = (int) $data->get('id_days_off');
        $this->daysOffService->removeDayForUser($username, $dayOffId);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeDayOffStatus() {
        $data = $this->getRequestData();
        $dayOffId = (int) $data->get('id_days_off');
        $this->daysOffService->changeDayOffStatus($dayOffId);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSpecialWorkdayStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->daysOffService->changeSpecialWorkdayStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\PrintsService as PrintsService;
use Services\UserService as UserService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of PrintsSchedule
 *
 * @author Tanzar
 */
class PrintsSchedule extends Controller{
    private PrintsService $prints;
    private UserService $user;

    public function __construct() {
        $this->prints = new PrintsService();
        $this->user = new UserService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_schedule');
        parent::__construct($privilages);
    }
    
    public function getUsers() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->user->getEmployedUsersListByMonthOrdered($month, $year);
        $this->setResponse($response);
    }
    
    public function generateAttendanceList() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $this->prints->generateAttendanceList($month, $year);
    }
    
    public function generateNotificationList() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $this->prints->generateNotificationList($month, $year);
    }
    
    public function generateTimesheets() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        if($data->isValueSet('username')){
            $username = $data->get('username');
            $this->prints->generateTimesheetsForUser($username, $month, $year);
        }
        else{
            $this->prints->generateTimesheetsForCurrentUser($month, $year);
        }
    }
    
    public function generateWorkCard() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        if($data->isValueSet('username')){
            $username = $data->get('username');
            $this->prints->generateWorkcardForUser($username, $month, $year);
        }
        else{
            $this->prints->generateWorkcardForCurrentUser($month, $year);
        }
    }
    
    
}

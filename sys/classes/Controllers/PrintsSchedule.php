<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\PrintsService as PrintsService;
use Services\UserService as UserService;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Logger\Logger as Logger;
use Custom\Logs\PrintsLog as PrintsLog;

/**
 * Description of PrintsSchedule
 *
 * @author Tanzar
 */
class PrintsSchedule extends Controller{
    private PrintsService $prints;
    private UserService $user;
    private Logger $logger;

    public function __construct() {
        $this->prints = new PrintsService();
        $this->user = new UserService();
        $this->logger = Logger::getInstance();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_schedule');
        $privilages->add('prints_schedule_reports');
        $privilages->add('prints_inspector');
        $privilages->add('prints_inspector_all_documents');
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
        $username = Session::getUsername();
        $entry = new PrintsLog('user: ' . $username . ' generated attendance list for month = ' . $month . ' and year = ' . $year);
        $this->logger->log($entry);
    }
    
    public function generateNotificationList() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $this->prints->generateNotificationList($month, $year);
        $username = Session::getUsername();
        $entry = new PrintsLog('user: ' . $username . ' generated notification list for month = ' . $month . ' and year = ' . $year);
        $this->logger->log($entry);
    }
    
    public function generateTimesheets() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        if($data->isValueSet('username')){
            $username = $data->get('username');
            $this->prints->generateTimesheetsForUser($username, $month, $year);
            $current = Session::getUsername();
            $entry = new PrintsLog('user: ' . $current . ' generated Timesheets '
                    . 'for user: ' . $username .  ' month = ' . $month . ' and year = ' . $year);
        }
        else{
            $this->prints->generateTimesheetsForCurrentUser($month, $year);
            $current = Session::getUsername();
            $entry = new PrintsLog('user: ' . $current . ' generated his/her Timesheets '
                    . 'for month = ' . $month . ' and year = ' . $year);
        }
        $this->logger->log($entry);
    }
    
    public function generateWorkCard() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        if($data->isValueSet('username')){
            $username = $data->get('username');
            $this->prints->generateWorkcardForUser($username, $month, $year);
            $current = Session::getUsername();
            $entry = new PrintsLog('user: ' . $current . ' generated Workcard '
                    . 'for user: ' . $username .  ' month = ' . $month . ' and year = ' . $year);
        }
        else{
            $this->prints->generateWorkcardForCurrentUser($month, $year);
            $current = Session::getUsername();
            $entry = new PrintsLog('user: ' . $current . ' generated his/her Workcard '
                    . 'for month = ' . $month . ' and year = ' . $year);
        }
        $this->logger->log($entry);
    }
    
    public function generateNightShiftReport() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $this->prints->generateNightShiftReport($month, $year);
        $username = Session::getUsername();
        $entry = new PrintsLog('user: ' . $username . ' generated night shift report'
                . ' for month = ' . $month . ' and year = ' . $year);
        $this->logger->log($entry);
    }
    
    
}

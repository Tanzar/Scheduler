<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Services\ActivityService as ActivityService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Datetime;

/**
 * Description of ScheduleUser
 *
 * @author Tanzar
 */
class ScheduleUser extends Controller{
    private ActivityService $activity;
    private ScheduleService $schedule;
    
    
    public function __construct() {
        $this->activity = new ActivityService();
        $this->schedule = new ScheduleService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('schedule_user');
        $privilages->add('schedule_admin');
        parent::__construct($privilages);
    }
    
    public function getActivities(){
        $result = $this->activity->getActivities();
        $this->setResponse($result);
    }
    
    public function getActivityGroups(){
        $result = $this->activity->getActivityGroups();
        $this->setResponse($result);
    }
    
    public function getAllEntries(){
        $data = $this->getRequestData();
        $start = $data->getValue('startDate');
        $end = $data->getValue('endDate');
        $result = $this->schedule->getEntries($start, $end);
        $this->setResponse($result);
    }
    
    public function getMyEntries(){
        $data = $this->getRequestData();
        $start = $data->getValue('startDate');
        $end = $data->getValue('endDate');
        $result = $this->schedule->getCurrentUserEntries($start, $end);
        $this->setResponse($result);
    }
    
    public function newEntry(){
        if($this->isUserAllowedToChange()){
            $data = $this->getRequestData();
            $id = $this->schedule->addEntryForCurrentUser($data);
            $response = new Container();
            $response->add('Entry added.', 'message');
            $response->add($id, 'id');
            $this->setResponse($response);
        }
        else{
            $limit = $this->getConfigValue('scheduleDaysLimit');
            $languages = Languages::getInstance();
            $this->throwException($languages->get('cannot_change_older') . 
                    $limit . ' ' . $languages->get('days') . '.');
        }
    }
    
    private function isUserAllowedToChange() : bool {
        $daysLimit = $this->getConfigValue('scheduleDaysLimit');
        $data = $this->getRequestData();
        $startDate = $data->getValue('start');
        $start = new DateTime($startDate);
        $limitDay = new DateTime('-' . $daysLimit . 'd');
        if($limitDay >= $start){
            if($this->currentUserHavePrivilage('admin') || 
                    $this->currentUserHavePrivilage('schedule_admin')){
                return true;
            }
            else{
                return false;
            }
        }
        else{
            return true;
        }
    }
}

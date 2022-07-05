<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Services\LocationService as LocationService;
use Services\DocumentService as DocumentService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;

/**
 * Description of ScheduleUser
 *
 * @author Tanzar
 */
class ScheduleUser extends Controller{
    private ScheduleService $schedule;
    private LocationService $location;
    private DocumentService $document;
    
    public function __construct() {
        $this->schedule = new ScheduleService();
        $this->location = new LocationService();
        $this->document = new DocumentService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('schedule_user');
        $privilages->add('schedule_user_inspector');
        $privilages->add('schedule_admin');
        parent::__construct($privilages);
    }
    
    public function getActivitiesForGroup(){
        $data = $this->getRequestData();
        $group = $data->get('activities_group');
        $result = $this->schedule->getActivitiesByGroup($group);
        $this->setResponse($result);
    }
    
    public function getLocationTypesForActivity(){
        $data = $this->getRequestData();
        $idActivity = $data->get('id_activity');
        $result = $this->schedule->getLocationTypeByIdActivity($idActivity);
        $this->setResponse($result);
    }
    
    public function getLocationsForType(){
        $data = $this->getRequestData();
        $idType = $data->get('id_location_type');
        $result = $this->location->getLocationsByTypeID($idType);
        $this->setResponse($result);
    }
    
    public function getAllEntries(){
        $data = $this->getRequestData();
        $start = $data->get('startDate');
        $end = $data->get('endDate');
        $result = $this->schedule->getEntries($start, $end);
        $this->setResponse($result);
    }
    
    public function getMyEntries(){
        $data = $this->getRequestData();
        $start = $data->get('startDate');
        $end = $data->get('endDate');
        $result = $this->schedule->getCurrentUserEntries($start, $end);
        $this->setResponse($result);
    }
    
    public function getMyMatchingDocuments(){
        $data = $this->getRequestData();
        $start = $data->get('start');
        $end = $data->get('end');
        $result = $this->document->getDocumentsForUserEntryDates($start, $end);
        $this->setResponse($result);
    }
    
    public function saveEntry(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $startDate = $data->get('start');
        if($this->isUserAllowedToChange($startDate)){
            $id = $this->schedule->saveEntryForCurrentUser($data);
            $response = new Container();
            $response->add($languages->get('changes_saved'), 'message');
            $response->add($id, 'id');
            $this->setResponse($response);
        }
        else{
            $limit = $this->getConfigValue('scheduleDaysLimit');
            $this->throwException($languages->get('cannot_change_older') . 
                    $limit . ' ' . $languages->get('days') . '.');
        }
    }
    
    public function saveLocation(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $name = $data->get('name');
        $locationTypeId = (int) $data->get('id_location_type');
        $id = $this->schedule->saveLocation($name, $locationTypeId);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
    }
    
    public function removeEntry(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $id = $data->get('id');
        $entry = $this->schedule->getEntryByID($id);
        if($this->isUserAllowedToChange($entry->get('start'))){
            $this->schedule->disableEntry($id);
            $response = new Container();
            $response->add($languages->get('data_removed'), 'message');
            $response->add($id, 'id');
            $this->setResponse($response);
        }
        else{
            $limit = $this->getConfigValue('scheduleDaysLimit');
            $this->throwException($languages->get('cannot_change_older') . 
                    $limit . ' ' . $languages->get('days') . '.');
        }
    }
    
    private function isUserAllowedToChange(string $startDate) : bool {
        $daysLimit = $this->getConfigValue('scheduleDaysLimit');
        $start = new DateTime($startDate);
        $limitDay = new DateTime();
        $limitDay->modify('-' . $daysLimit . ' day');
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

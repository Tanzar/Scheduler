<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Services\LocationService as LocationService;
use Services\DocumentService as DocumentService;
use Custom\Blockers\ScheduleBlocker as ScheduleBlocker;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

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
        $result = $this->document->getDocumentsByUserEntryDetails($data);
        $this->setResponse($result);
    }
    
    public function saveEntry(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $id = $this->schedule->saveEntryForCurrentUser($data);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
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
        $this->schedule->disableEntry($id);
        $response = new Container();
        $response->add($languages->get('data_removed'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
    }
}

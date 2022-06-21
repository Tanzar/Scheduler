<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Services\LocationService as LocationService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;

/**
 * Description of ScheduleAdmin
 *
 * @author Tanzar
 */
class ScheduleAdmin extends Controller{
    private ScheduleService $schedule;
    private LocationService $location;
    
    
    public function __construct() {
        $this->schedule = new ScheduleService();
        $this->location = new LocationService();
        $privilages = new Container();
        $privilages->add('admin');
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
    
    public function getEntries(){
        $data = $this->getRequestData();
        $start = $data->get('startDate');
        $end = $data->get('endDate');
        $username = $data->get('username');
        $result = $this->schedule->getUserEntries($username, $start, $end);
        $this->setResponse($result);
    }
    
    public function saveEntry(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $id = $this->schedule->saveEntryForUser($data);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
    }
    
    public function removeEntry(){
        $languages = Languages::getInstance();
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->schedule->disableEntry($id);
        $response = new Container();
        $response->add($languages->get('data_removed'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
    }
    
}

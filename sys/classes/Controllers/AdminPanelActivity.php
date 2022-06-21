<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Services\LocationService as LocationService;
use Services\ActivityLocationAssignment as ActivityLocationAssignment;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelActivity
 *
 * @author Tanzar
 */
class AdminPanelActivity extends Controller{
    private ScheduleService $schedule;
    private LocationService $location;
    
    public function __construct() {
        $this->schedule = new ScheduleService();
        $this->location = new LocationService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getActivities(){
        $data = $this->schedule->getAllActivities();
        $this->setResponse($data);
    }
    
    public function getActivityGroups(){
        $data = $this->schedule->getAllActivityGroups();
        $this->setResponse($data);
    }
    
    public function getLocationTypes(){
        $data = $this->location->getAllLocationTypes();
        $this->setResponse($data);
    }
    
    public function getLocationTypesForActivity(){
        $data = $this->getRequestData();
        $idActivity = $data->get('id');
        $response = $this->schedule->getLocationTypeByIdActivity($idActivity);
        $this->setResponse($response);
    }
    
    public function saveActivity(){
        $data = $this->getRequestData();
        $id = $this->schedule->saveActivity($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveActivityLocationTypes() {
        $data = $this->getRequestData();
        $idActivity = $data->get('id_activity');
        $locationTypes = new Container($data->get('location_types'));
        $this->schedule->assign($idActivity, $locationTypes);
        $languages = Languages::getInstance();
        $response = new COntainer();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeActivityStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->schedule->changeActivityStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

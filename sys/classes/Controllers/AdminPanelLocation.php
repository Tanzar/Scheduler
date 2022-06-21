<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\LocationService as LocationService;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;
use Tanweb\Utility as Utility;

/**
 * Description of AdminPanelLocations
 *
 * @author Tanzar
 */
class AdminPanelLocation extends Controller{
    private LocationService $location;
    
    
    public function __construct() {
        $this->location = new LocationService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getLocations(){
        $data = $this->location->getAllLocations();
        $this->setResponse($data);
    }
    
    public function getLocationGroups(){
        $data = $this->location->getAllLocationGroups();
        $this->setResponse($data);
    }
    
    public function getActiveLocationGroups(){
        $data = $this->location->getActiveLocationGroups();
        $this->setResponse($data);
    }
    
    public function getLocationTypes(){
        $data = $this->location->getAllLocationTypes();
        $this->setResponse($data);
    }
    
    public function getActiveLocationTypes(){
        $data = $this->location->getActiveLocationTypes();
        $this->setResponse($data);
    }
    
    public function saveLocation(){
        $data = $this->getRequestData();
        $id = $this->location->saveLocation($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveLocationGroup(){
        $data = $this->getRequestData();
        $id = $this->location->saveLocationGroup($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveLocationType(){
        $data = $this->getRequestData();
        $id = $this->location->saveLocationType($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeLocationStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->location->changeLocationStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeLocationTypeStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->location->changeLocationTypeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeLocationGroupStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->location->changeLocationGroupStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

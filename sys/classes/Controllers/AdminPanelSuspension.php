<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\SuspensionService as SuspensionService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelSuspension
 *
 * @author Tanzar
 */
class AdminPanelSuspension extends Controller{
    private SuspensionService $suspension;
    
    public function __construct() {
        $this->suspension = new SuspensionService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getAllSuspensionTypesByGroup() {
        $data = $this->getRequestData();
        $groupId = (int) $data->get('id_suspension_suzug_group');
        $response = $this->suspension->getAllTypesByGroupId($groupId);
        $this->setResponse($response);
    }
    
    public function getAllSuspensionGroups() {
        $response = $this->suspension->getAllGroups();
        $this->setResponse($response);
    }
    
    public function getActiveSuspensionGroups() {
        $response = $this->suspension->getActiveGroups();
        $this->setResponse($response);
    }
    
    public function getAllSuspensionReasons() {
        $response = $this->suspension->getAllReasons();
        $this->setResponse($response);
    }
    
    public function getAllSuspensionObjects() {
        $response = $this->suspension->getAllObjects();
        $this->setResponse($response);
    }
    
    public function saveSuspensionType() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveType($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSuspensionGroup() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveGroup($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSuspensionReason() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveReason($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSuspensionObject() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveObject($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSuspensionTypeStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeTypeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSuspensionGroupStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeGroupStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSuspensionReasonStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeReasonStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSuspensionObjectStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeObjectStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

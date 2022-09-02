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
    
    public function getAllSuspensionSuzugTypesByGroup() {
        $data = $this->getRequestData();
        $groupId = (int) $data->get('id_suspension_suzug_group');
        $response = $this->suspension->getAlSuzuglTypesByGroupId($groupId);
        $this->setResponse($response);
    }
    
    public function getAllSuspensionSuzugGroups() {
        $response = $this->suspension->getAllSuzugGroups();
        $this->setResponse($response);
    }
    
    public function getActiveSuspensionSuzugGroups() {
        $response = $this->suspension->getActiveSuzugGroups();
        $this->setResponse($response);
    }
    
    public function getAllSuspensionReasons() {
        $response = $this->suspension->getAllReasons();
        $this->setResponse($response);
    }
    
    public function saveSuspensionSuzugType() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveSuzugType($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSuspensionSuzugGroup() {
        $data = $this->getRequestData();
        $id = $this->suspension->saveSuzugGroup($data);
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
    
    public function changeSuspensionSuzugTypeStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeSuzugTypeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSuspensionSuzugGroupStatus() {
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->suspension->changeSuzugGroupStatus($id);
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
}

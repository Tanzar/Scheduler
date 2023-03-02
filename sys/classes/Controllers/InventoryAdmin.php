<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Services\InventoryService as InventoryService;
use Services\UserService as UserService;

/**
 * Description of InventoryAdmin
 *
 * @author Tanzar
 */
class InventoryAdmin extends Controller{
    private InventoryService $inventory;
    private UserService $user;
    
    public function __construct() {
        $this->inventory = new InventoryService();
        $this->user = new UserService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('inventory_admin');
        parent::__construct($privilages);
    }
    
    public function getUserEquipment() {
       $data = $this->getRequestData();
       $username = $data->get('username');
       $response = $this->inventory->getUserEquipment($username);
       $this->setResponse($response);
    }
    
    public function getEquipmentByType() {
        $data = $this->getRequestData();
        $idType = $data->get('id_equipment_type');
        $response = $this->inventory->getEquipmentByType($idType);
        $this->setResponse($response);
    }
    
    public function getEquipmentByState() {
        $data = $this->getRequestData();
        $idState = $data->get('id_equipment_state');
        $response = $this->inventory->getEquipmentByState($idState);
        $this->setResponse($response);
    }
    
    public function getEquipmentByInventoryNumber() {
        $data = $this->getRequestData();
        $number = $data->get('inventory_number');
        $response = $this->inventory->getEquipmentByInventoryNumber($number);
        $this->setResponse($response);
    }
    
    public function getEquipmentByRemarks() {
        $data = $this->getRequestData();
        $remarks = $data->get('remarks');
        $response = $this->inventory->getEquipmentByRemarks($remarks);
        $this->setResponse($response);
    }
    
    public function getAllEquipment() {
       $response = $this->inventory->getActiveEquipment();
       $this->setResponse($response);
    }
    
    public function getUsersList() {
       $response = $this->user->getEmployedUsersListOrdered(date('Y-m-d'));
       $this->setResponse($response);
    }
    
    public function getUsersExcept() {
        $data = $this->getRequestData();
        $username = $data->get('username');
        $response = $this->user->getActiveUsersExcept($username);
        $this->setResponse($response);
    }
    
    public function getEquipmentTypes() {
       $response = $this->inventory->getActiveEquipmentTypes();
       $this->setResponse($response);
    }
   
    public function getEquipmentStates() {
       $response = $this->inventory->getEquipmentStates();
       $this->setResponse($response);
    }
    
    public function getNewEquipmentDetails() {
        $response = $this->inventory->getNewEquipmentDetails();
        $this->setResponse($response);
    }
    
    public function saveNewEquipment(){
        $data = $this->getRequestData();
        $id = $this->inventory->saveNewEquipment($data);
        $this->setSuccesResponse($id);
    }
    
    public function saveNewBorrowedEquipment(){
        $data = $this->getRequestData();
        $id = $this->inventory->saveNewBorrowedEquipment($data);
        $this->setSuccesResponse($id);
    }
    
    public function editEquipment(){
        $data = $this->getRequestData();
        $this->inventory->editEquipment($data);
        $this->setSuccesResponse();
    }
    
    public function assignToUser() {
        $data = $this->getRequestData();
        $this->inventory->assignEquipment($data, true);
        $this->setSuccesResponse();
    }
    
    public function sendToRepair() {
        $data = $this->getRequestData();
        $date = $data->get('date');
        $document = $data->get('document');
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->sendToRepair($date, $document, $equipmentId);
        $this->setSuccesResponse();
    }
    
    public function returnFromRepair() {
        $data = $this->getRequestData();
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->returnFromRepair($equipmentId);
        $this->setSuccesResponse();
    }
    
    public function sendToCalibration() {
        $data = $this->getRequestData();
        $date = $data->get('date');
        $document = $data->get('document');
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->sendToCalibration($date, $document, $equipmentId);
        $this->setSuccesResponse();
    }
    
    public function returnFromCalibration() {
        $data = $this->getRequestData();
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->returnFromCalibration($equipmentId);
        $this->setSuccesResponse();
    }
    
    public function liquidation() {
        $data = $this->getRequestData();
        $date = $data->get('date');
        $document = $data->get('document');
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->liquidate($date, $document, $equipmentId);
        $this->setSuccesResponse();
    }
    
    public function returnFromLiquidation() {
        $data = $this->getRequestData();
        $equipmentId = (int) $data->get('id_equipment');
        $this->inventory->returnFromLiquidation($equipmentId);
        $this->setSuccesResponse();
    }
    
    private function setSuccesResponse(int $id = 0) {
        $languages = Languages::getInstance();
        $response = new Container();
        if($id !== 0) {
            $response->add($id, 'id');
        }
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

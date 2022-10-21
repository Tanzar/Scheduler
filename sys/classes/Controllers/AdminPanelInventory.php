<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Services\InventoryService as InventoryService;

/**
 * Description of AdminPanelInventory
 *
 * @author Tanzar
 */
class AdminPanelInventory extends Controller{
    private InventoryService $inventory;
    
    public function __construct() {
        $this->inventory = new InventoryService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getEquipmentTypes() {
        $response = $this->inventory->getAllEquipmentTypes();
        $this->setResponse($response);
    }
    
    public function saveEquipmentType() {
        $data = $this->getRequestData();
        $id = $this->inventory->saveEquipmentType($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeEquipmentTypeStatus(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->inventory->changeEquipmentTypeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

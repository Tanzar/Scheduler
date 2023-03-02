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
use Tanweb\Session as Session;

/**
 * Description of InventoryMy
 *
 * @author Tanzar
 */
class InventoryMy  extends Controller{
    private InventoryService $inventory;
    private UserService $user;
    
    public function __construct() {
        $this->inventory = new InventoryService();
        $this->user = new UserService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('inventory_admin');
        $privilages->add('inventory_user');
        parent::__construct($privilages);
    }
    
    public function getMyEquipment() {
        $response = $this->inventory->getCurrentUserEquipment();
        $this->setResponse($response);
    }
    
    public function getMyUnconfirmedEquipment() { 
        $response = $this->inventory->getCurrentUserUnconfirmed();
        $this->setResponse($response);
    }
    
    public function getUsers() {
        $username = Session::getUsername();
        $response = $this->user->getActiveUsersWithoutSystemAndExcept($username);
        $this->setResponse($response);
    }
    
    public function assignToUser() {
        $data = $this->getRequestData();
        $this->inventory->assignEquipment($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function confirmAssign() {
        $data = $this->getRequestData();
        $this->inventory->confirmEquipmentAssign($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function cancelAssign() {
        $data = $this->getRequestData();
        $this->inventory->cancelEquipmentAssign($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

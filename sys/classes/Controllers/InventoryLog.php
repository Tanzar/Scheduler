<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\InventoryService as InventoryService;
use Services\UserService as UserService;

/**
 * Description of InventoryLog
 *
 * @author Tanzar
 */
class InventoryLog  extends Controller{
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
    
    public function getUsers() {
        $response = $this->user->getActiveUsers();
        $this->setResponse($response);
    }
    
    public function getTodayLogs() {
        $response = $this->inventory->getTodayLogs();
        $this->setResponse($response);
    }
    
    public function getFilteredLogs() {
        $data = $this->getRequestData();
        $filters = new Container($data->get('filters'));
        $response = $this->inventory->getLogsByFilters($filters);
        $this->setResponse($response);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ScheduleService as ScheduleService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;


/**
 * Description of AdminPanelSchedule
 *
 * @author Tanzar
 */
class AdminPanelSchedule extends Controller{
    private ScheduleService $schedule;
    
    
    public function __construct() {
        $this->schedule = new ScheduleService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getUserEntries(){
        $data = $this->getRequestData();
        $username = $data->get('username');
        $start = $data->get('startDate');
        $end = $data->get('endDate');
        $response = $this->schedule->getAllUserEntries($username, $start, $end);
        $this->setResponse($response);
    }
    
    public function changeEntryStatus(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->schedule->changeEntryStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

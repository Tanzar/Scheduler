<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\OvertimeService as OvertimeService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;


/**
 * Description of AdminPanelOvertime
 *
 * @author Tanzar
 */
class AdminPanelOvertime extends Controller{
    private OvertimeService $overtimeService;
    
    public function __construct() {
        $this->overtimeService = new OvertimeService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getOvertimeReductions() {
        $response = $this->overtimeService->getALL();
        $this->setResponse($response);
    }
    
    public function getOptions() {
        $response = $this->overtimeService->getOptions();
        $this->setResponse($response);
    }
    
    public function saveOvertimeReduction() {
        $data = $this->getRequestData();
        $id = $this->overtimeService->save($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeOvertimeReductionStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->overtimeService->changeStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

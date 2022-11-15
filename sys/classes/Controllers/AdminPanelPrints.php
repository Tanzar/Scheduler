<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Services\NightShiftReportService as NightShiftReportService;
use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelPrints
 *
 * @author Tanzar
 */
class AdminPanelPrints extends Controller{
    private NightShiftReportService $nightShiftReportService;
    
    public function __construct() {
        $this->nightShiftReportService = new NightShiftReportService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getNightShiftReportNumbers() : void {
        $response = $this->nightShiftReportService->getReportNumbers();
        $this->setResponse($response);
    }
    
    public function getYear() : void {
        $year = $this->nightShiftReportService->getStartYear();
        $response = new Container();
        $response->add($year, 'year');
        $this->setResponse($response);
    }
    
    public function saveNightShiftReportNumber() : void {
        $data = $this->getRequestData();
        $id = $this->nightShiftReportService->saveReportNumber($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeNightShiftReportNumberStatus() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->nightShiftReportService->changeReportNumberStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

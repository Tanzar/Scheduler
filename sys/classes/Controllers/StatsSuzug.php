<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\SuzugService as SuzugService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of StatsSuzug
 *
 * @author Tanzar
 */
class StatsSuzug extends Controller{
    private SuzugService $suzugService;
    
    public function __construct() {
        $this->suzugService = new SuzugService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('stats_admin');
        parent::__construct($privilages);
    }
    
    public function getSuzugUsers() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->suzugService->getAllSuzugUsersForYear($year);
        $this->setResponse($response);
    }
    
    public function getAssignmentOptions() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->suzugService->getAssignmentOptions($year);
        $this->setResponse($response);
    }
    
    public function save() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->suzugService->save($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->suzugService->changeSuzugUserStatus($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

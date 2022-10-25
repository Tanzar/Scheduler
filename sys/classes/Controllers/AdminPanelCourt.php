<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\CourtApplicationService as CourtApplicationService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelCourt
 *
 * @author Tanzar
 */
class AdminPanelCourt extends Controller{
    private CourtApplicationService $courtApplication;
    
    public function __construct() {
        $this->courtApplication = new CourtApplicationService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getCourtApplications() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $username = $data->get('username');
        $response = $this->courtApplication->getAllUserApplictionsForYear($username, $year);
        $this->setResponse($response);
    }
    
    public function getEditApplicationDetails() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->courtApplication->getNewApplicationDetails($documentId);
        $this->setResponse($response);
    }
    
    public function saveCourtApplication() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->courtApplication->saveApplication($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeApplicationStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->courtApplication->changeApplicationStatus($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

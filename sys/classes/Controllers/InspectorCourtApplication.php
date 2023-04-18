<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\CourtApplicationService as CourtApplicationService;
use Services\DocumentService as DocumentService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorCourtApplication
 *
 * @author Tanzar
 */
class InspectorCourtApplication extends Controller{
    private DocumentService $documentService;
    private CourtApplicationService $courtApplication;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->courtApplication = new CourtApplicationService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('schedule_user_inspector');
        parent::__construct($privilages);
    }
    
    public function getDocuments() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->documentService->getCurrentUserDocumentsByYear($year);
        $this->setResponse($response);
    }
    
    public function getCourtApplications() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->courtApplication->getApplicationsForCurrentUser($documentId);
        $this->setResponse($response);
    }
    
    public function getCourtApplicationsByYear() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->courtApplication->getCurrentUserActiveApplicationsByYear($year);
        $this->setResponse($response);
    }
    
    public function getNewApplicationDetails() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->courtApplication->getNewApplicationDetails($documentId);
        $this->setResponse($response);
    }
    
    public function saveNewCourtApplication() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->courtApplication->saveNewApplication($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
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
    
    public function removeApplication() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->courtApplication->disable($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

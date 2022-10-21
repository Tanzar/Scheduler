<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\DocumentService as DocumentService;
use Services\InstrumentUsageService as InstrumentUsageService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorInstruments
 *
 * @author Tanzar
 */
class InspectorInstruments extends Controller{
    private DocumentService $documentService;
    private InstrumentUsageService $instrumentService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->instrumentService = new InstrumentUsageService();
        $privilages = new Container(['admin', 'schedule_user_inspector']);
        parent::__construct($privilages);
    }
    
    public function getDocuments() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->documentService->getCurrentUserDocumentsByMonthYear($month, $year);
        $this->setResponse($response);
    }
    
    public function getUsages() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->instrumentService->getUsagedForCurrentUser($documentId);
        $this->setResponse($response);
    }
    
    public function getNewUsageDetails() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->instrumentService->getNewUsageDetails($documentId);
        $this->setResponse($response);
    }
    
    public function saveNewUsage() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->instrumentService->saveUsageForCurrentUser($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function updateUsage() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $this->instrumentService->updateUsage($data);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeUsage() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->instrumentService->disableUsage($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

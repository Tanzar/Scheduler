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
 * Description of AdminPanelUsages
 *
 * @author Tanzar
 */
class AdminPanelUsages extends Controller{
    private DocumentService $documentService;
    private InstrumentUsageService $instrumentService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->instrumentService = new InstrumentUsageService();
        $privilages = new Container(['admin']);
        parent::__construct($privilages);
    }
    
    public function getUsages() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $username = $data->get('username');
        $response = $this->instrumentService->getAllUserUsagesForYear($username, $year);
        $this->setResponse($response);
    }
    
    public function getEditUsageDetails() {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->instrumentService->getNewUsageDetails($documentId);
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
    
    public function changeUsageStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->instrumentService->changeUsageStatus($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

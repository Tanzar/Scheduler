<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\SuspensionService as SuspensionService;
use Services\DocumentService as DocumentService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorSuspensions
 *
 * @author Tanzar
 */
class InspectorSuspensions extends Controller{
    private DocumentService $documentService;
    private SuspensionService $suspensionService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->suspensionService = new SuspensionService();
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
    
    public function getSuspensions() {
        $data = $this->getRequestData();
        $idDocument = (int) $data->get('id_document');
        $response = $this->suspensionService->getCurrentUserSuspensions($idDocument);
        $this->setResponse($response);
    }
    
}

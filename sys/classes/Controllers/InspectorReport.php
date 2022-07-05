<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\DocumentService as DocumentService;
use Services\InspectionReportService as InspectionReportService;
use Tanweb\Session as Session;

/**
 * Description of InspectorReport
 *
 * @author Tanzar
 */
class InspectorReport extends Controller{
    private DocumentService $documentService;
    private InspectionReportService $reportService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->reportService = new InspectionReportService();
        $privilages = new Container(['admin', 'schedule_user_inspector']);
        parent::__construct($privilages);
    }
    
    public function getMyDocuments(){
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $result = $this->documentService->getCurrentUserDocumentsByYear($year);
        $this->setResponse($result);
    }
    
    public function generateReport(){
        $data = $this->getRequestData();
        $documentId = (int) $data->get('documentId');
        if($data->isValueSet('username')){
            $username = $data->get('username');
        }
        else{
            $username = Session::getUsername();
        }
        $result = $this->reportService->generateReport($documentId, $username);
        $this->setResponse($result);
    }
}

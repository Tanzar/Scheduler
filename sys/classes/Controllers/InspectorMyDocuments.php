<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\DocumentService as DocumentService;

/**
 * Description of InspectorMyDocuments
 *
 * @author Tanzar
 */
class InspectorMyDocuments extends Controller{
    private DocumentService $documentService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $privilages = new Container(['admin', 'schedule_user_inspector']);
        parent::__construct($privilages);
    }
    
    public function getMyDocuments(){
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $documents = $this->documentService->getCurrentUserDocumentsByMonthYear($month, $year);
        $this->setResponse($documents);
    }
    
    public function editDocument(){
        $data = $this->getRequestData();
        $message = $this->documentService->editDocument($data);
        $responsse = new Container();
        $responsse->add($message, 'message');
        $this->setResponse($responsse);
    }
}

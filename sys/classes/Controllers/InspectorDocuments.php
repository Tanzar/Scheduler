<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\DocumentService as DocumentService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorDocuments
 *
 * @author Tanzar
 */
class InspectorDocuments extends Controller{
    private DocumentService $documentService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $privilages = new Container(['admin', 'schedule_user_inspector']);
        parent::__construct($privilages);
    }
    
    public function getDocumentsForMonthYear(){
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $documents = $this->documentService->getDocumentsByMonthYear($month, $year);
        $this->setResponse($documents);
    }
    
    public function getDocumentUsers(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $users = $this->documentService->getUsersByDocumentId($id);
        $this->setResponse($users);
    }
    
    public function saveAndAssignDocument(){
        $data = $this->getRequestData();
        $id = $this->documentService->saveAndAssignDocument($data);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $response->add($id, 'id');
        $this->setResponse($response);
    }
    
    public function assignUser(){
        $data = $this->getRequestData();
        $id = $data->get('id');
        $this->documentService->assignCurrentUserToDocument($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\DocumentService as DocumentService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelDocuments
 *
 * @author Tanzar
 */
class AdminPanelDocuments extends Controller{
    private DocumentService $docuement;
    
    public function __construct() {
        $this->docuement = new DocumentService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getDocuments(){
        $data = $this->getRequestData();
        $month = $data->get('month');
        $year = $data->get('year');
        $response = $this->docuement->getAllDocumentsByMonthYear($month, $year);
        $this->setResponse($response);
    }
    
    public function getDocumentUsers(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->docuement->getUsersByDocumentId($id);
        $this->setResponse($response);
    }
    
    public function changeDocumentStatus(){
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->docuement->changeDocumentStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveDocument(){
        $data = $this->getRequestData();
        $id = $this->docuement->saveDocument($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function unassignUserFromDocument(){
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $username = $data->get('username');
        $this->docuement->unassignUserFromDocument($username, $documentId);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

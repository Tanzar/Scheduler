<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\DecisionService as DecisionService;
use Services\DocumentService as DocumentService;
use Services\SuspensionService as SuspensionService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorDecisions
 *
 * @author Tanzar
 */
class InspectorDecisions extends Controller{
    private DocumentService $documentService;
    private DecisionService $decisionService;
    private SuspensionService $suspensionService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->decisionService = new DecisionService();
        $this->suspensionService = new SuspensionService();
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
    
    public function getDecisions() : void {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id_document');
        $response = $this->decisionService->getCurrentUserDecisions($documentId);
        $this->setResponse($response);
    }
    
    public function getDecisionsByYear() {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $response = $this->decisionService->getCurrentUserActiveDecisionsByYear($year);
        $this->setResponse($response);
    }
    
    public function getNewDecisionDetails() : void {
        $data = $this->getRequestData();
        $documentId = $data->get('id_document');
        $response = $this->decisionService->getNewDecisionData($documentId);
        $this->setResponse($response);
    }
    
    public function getSuspensions() : void {
        $data = $this->getRequestData();
        $idDocument = (int) $data->get('id_document');
        $suspensions = $this->suspensionService->getCurrentUserSuspensions($idDocument);
        $this->setResponse($suspensions);
    }
    
    public function saveDecision() : void {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->decisionService->saveDecisionForCurrentUser($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeDecision() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->decisionService->removeDecision($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

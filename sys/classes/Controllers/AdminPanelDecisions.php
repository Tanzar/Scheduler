<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\DecisionService as DecisionService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelDecisions
 *
 * @author Tanzar
 */
class AdminPanelDecisions extends Controller{
    private DecisionService $decisionService;
    
    public function __construct() {
        $this->decisionService = new DecisionService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getAllDecisionLaws() : void {
        $response = $this->decisionService->getAllDecisionLaws();
        $this->setResponse($response);
    }
    
    public function getAllUserDecisionsByYear() : void {
        $data = $this->getRequestData();
        $username = $data->get('username');
        $year = (int) $data->get('year');
        $response = $this->decisionService->getAllUserDecisionsForYear($username, $year);
        $this->setResponse($response);
    }
    
    
    public function getEditDecisionDetails() : void {
        $data = $this->getRequestData();
        $documentId = $data->get('id_document');
        $response = $this->decisionService->getNewDecisionData($documentId);
        $this->setResponse($response);
    }
    
    public function saveDecision() : void {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->decisionService->saveDecision($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveDecisionLaw() : void {
        $data = $this->getRequestData();
        $id = $this->decisionService->saveDecisionLaw($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeDecisionLawStatus() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->decisionService->changeDecisionLawStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeDecisionStatus() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->decisionService->changeDecisionStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\SuspensionService as SuspensionService;
use Services\DocumentService as DocumentService;
use Services\ArticleService as ArticleService;
use Services\TicketService as TicketService;
use Services\DecisionService as DecisionService;
use Tanweb\Config\INI\Languages as Languages;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;

/**
 * Description of InspectorSuspensions
 *
 * @author Tanzar
 */
class InspectorSuspensions extends Controller{
    private DocumentService $documentService;
    private SuspensionService $suspensionService;
    private ArticleService $articleSerivce;
    private TicketService $ticketService;
    private DecisionService $decisionService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->suspensionService = new SuspensionService();
        $this->articleSerivce = new ArticleService();
        $this->ticketService = new TicketService();
        $this->decisionService = new DecisionService();
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
    
    public function getNewSuspensionDetails(){
        $data = $this->getRequestData();
        $idDocument = (int) $data->get('id_document');
        $response = $this->suspensionService->getSuspensionDetails($idDocument);
        $this->setResponse($response);
    }
    
    public function getMyArticles() {
        $data = $this->getRequestData();
        $documentId = $data->get('id_document');
        $articles = $this->articleSerivce->getCurrentUserActiveArticlesByDocument($documentId);
        $this->setResponse($articles);
    }
    
    public function getAssignedArticles() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $articles = $this->suspensionService->getAssignedCurrentUserArticles($idSuspension);
        $this->setResponse($articles);
    }
    
    public function getMyTickets() {
        $data = $this->getRequestData();
        $documentId = $data->get('id_document');
        $tickets = $this->ticketService->getCurrentUserActiveTicketsByDocument($documentId);
        $this->setResponse($tickets);
    }
    
    public function getAssignedTickets() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $tickets = $this->suspensionService->getAssignedCurrentUserTickets($idSuspension);
        $this->setResponse($tickets);
    }
    
    public function getMyDecisions() {
        $data = $this->getRequestData();
        $documentId = $data->get('id_document');
        $decisions = $this->decisionService->getCurrentUserDecisionsRequiringSuspensions($documentId);
        $this->setResponse($decisions);
    }
    
    public function getAssignedDecisions() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $decisions = $this->suspensionService->getAssignedCurrentUserDecisions($idSuspension);
        $this->setResponse($decisions);
    }
    
    public function assignExistingArticle() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idArticle = (int) $data->get('id_art_41');
        $languages = Languages::getInstance();
        if($this->isArticleBlocked($idArticle)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $id = $this->suspensionService->assignArticle($idSuspension, $idArticle);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function unassignArticle() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idArticle = (int) $data->get('id_art_41');
        $languages = Languages::getInstance();
        if($this->isArticleBlocked($idArticle)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $this->suspensionService->unassignArticle($idSuspension, $idArticle);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function assignExistingTicket() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idTicket = (int) $data->get('id_ticket');
        $languages = Languages::getInstance();
        if($this->isTicketBlocked($idTicket)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $id = $this->suspensionService->assignTicket($idSuspension, $idTicket);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function unassignTicket() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idTicket = (int) $data->get('id_ticket');
        $languages = Languages::getInstance();
        if($this->isArticleBlocked($idTicket)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $this->suspensionService->unassignTicket($idSuspension, $idTicket);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function assignExistingDecision() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idDecision = (int) $data->get('id_decision');
        $languages = Languages::getInstance();
        if($this->isDecisionBlocked($idDecision)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $id = $this->suspensionService->assignDecision($idSuspension, $idDecision);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function unassignDecision() {
        $data = $this->getRequestData();
        $idSuspension = (int) $data->get('id_suspension');
        $idDecision = (int) $data->get('id_decision');
        $languages = Languages::getInstance();
        if($this->isDecisionBlocked($idDecision)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        $this->suspensionService->unassignDecision($idSuspension, $idDecision);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSuspension() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        else{
            $id = $this->suspensionService->saveSuspensionForCurrentUser($data);
        }
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeSuspension() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
        else{
            $this->suspensionService->disableSuspesnion($id);
        }
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    private function isArticleBlocked(int $id) : bool {
        $blocker = new InspectorDateBlocker();
        $art = $this->articleSerivce->getById($id);
        if($blocker->isBLocked($art)){
            return true;
        }
        else{
            return false;
        }
    }
    
    private function isTicketBlocked(int $id) : bool {
        $blocker = new InspectorDateBlocker();
        $ticket = $this->ticketService->getById($id);
        if($blocker->isBLocked($ticket)){
            return true;
        }
        else{
            return false;
        }
    }
    
    private function isDecisionBlocked(int $id) : bool {
        $blocker = new InspectorDateBlocker();
        $decision = $this->decisionService->getById($id);
        if($blocker->isBLocked($decision)){
            return true;
        }
        else{
            return false;
        }
    }
}

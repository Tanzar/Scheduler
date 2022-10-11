<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\DecisionDAO as DecisionDAO;
use Data\Access\Tables\DecisionLawDAO as DecisionLawDAO;
use Data\Access\Tables\SuspensionDecisionDAO as SuspensionDecisionDAO;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Config\INI\Languages as Languages;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;

/**
 * Description of DecisionService
 *
 * @author Tanzar
 */
class DecisionService {
    private DecisionDAO $decision;
    private DecisionLawDAO $decisionLaw;
    private SuspensionDecisionDAO $suspensionDecision;
    private DecisionDetailsView $decisionDetails;
    private DocumentUserDetailsView $documentUserDetails;
    
    public function __construct() {
        $this->decision = new DecisionDAO();
        $this->decisionLaw = new DecisionLawDAO();
        $this->suspensionDecision = new SuspensionDecisionDAO();
        $this->decisionDetails = new DecisionDetailsView();
        $this->documentUserDetails = new DocumentUserDetailsView();
    }
    
    public function getById(int $id) : Container {
        return $this->decisionDetails->getById($id);
    }
    
    public function getAllDecisionLaws() : Container {
        return $this->decisionLaw->getAll();
    }
    
    public function getAllUserDecisionsForYear(string $username, int $year) : Container {
        return $this->decisionDetails->getAllByUsernameAndYear($username, $year);
    }
    
    public function getCurrentUserDecisions(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->decisionDetails->getActiveByUsernameAndDocumentId($username, $documentId);
    }
    
    public function getCurrentUserDecisionsRequiringSuspensions(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->decisionDetails
                ->getActiveRequiringSuspensionByUsernameAndDocumentId($username, $documentId);
    }
    
    public function saveDecisionLaw(Container $data) : int {
        return $this->decisionLaw->save($data);
    }
    
    public function getNewDecisionData(int $documentId) : Container {
        $relations = $this->documentUserDetails->getAllByDocumentId($documentId);
        $relation = new Container($relations->get(0));
        $start = $relation->get('start');
        $end = $relation->get('end');
        $laws = $this->decisionLaw->getActive();
        $result = new Container();
        $result->add($start, 'start');
        $result->add($end, 'end');
        $result->add($laws->toArray(), 'laws');
        return $result;
    }
    
    public function saveDecision(Container $data) : int {
        $this->checkBlocker($data);
        $decision = $this->formDecision($data);
        $id = $this->decision->save($decision);
        if($data->isValueSet('id_suspension')){
            $assign = new Container();
            $assign->add($id, 'id_decision');
            $assign->add($data->get('id_suspension'), 'id_suspension');
            $this->suspensionDecision->save($assign);
        }
        return $id;
    }
    
    public function saveDecisionForCurrentUser(Container $data) : int {
        $this->checkBlocker($data);
        if(!$data->isValueSet('id_document_user')){
            $username = Session::getUsername();
            $documentId = $data->get('id_document');
            $documentUserId = $this->getDocumentUserId($username, $documentId);
            $data->add($documentUserId, 'id_document_user');
        }
        $decision = $this->formDecision($data);
        $id = $this->decision->save($decision);
        if($data->isValueSet('id_suspension')){
            $assign = new Container();
            $assign->add($id, 'id_decision');
            $assign->add($data->get('id_suspension'), 'id_suspension');
            $this->suspensionDecision->save($assign);
        }
        return $id;
    }
    
    private function getDocumentUserId(string $username, int $documentId) : int {
        $relation = $this->documentUserDetails->getByUsernameAndDocumentId($username, $documentId);
        $id = (int) $relation->get('id_document_user');
        return $id;
    }
    
    public function changeDecisionLawStatus(int $id) : void {
        $law = $this->decisionLaw->getById($id);
        $active = $law->get('active');
        if($active){
            $this->decisionLaw->disable($id);
        }
        else{
            $this->decisionLaw->enable($id);
        }
    }
    
    public function changeDecisionStatus(int $id) : void {
        $decision = $this->decision->getById($id);
        $active = $decision->get('active');
        if($active){
            $this->decision->disable($id);
        }
        else{
            $this->decision->enable($id);
        }
    }
    
    public function removeDecision(int $id) : void {
        $decision = $this->decision->getById($id);
        $this->checkBlocker($decision);
        $this->decision->disable($id);
    }
    
    private function formDecision(Container $data) : Container {
        $decision = new Container();
        if($data->isValueSet('id')){
            $decision->add($data->get('id'), 'id');
        }
        $decision->add($data->get('date'), 'date');
        $decision->add($data->get('description'), 'description');
        $decision->add($data->get('remarks'), 'remarks');
        $decision->add($data->get('id_decision_law'), 'id_decision_law');
        $decision->add($data->get('id_document_user'), 'id_document_user');
        return $decision;
    }
    
    private function checkBlocker(Container $data) {
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            $languages = Languages::getInstance();
            $this->throwException($languages->get('cannot_change_selected_month'));
        }
    }
}

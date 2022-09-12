<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\SuspensionGroupDAO as SuspensionGroupDAO;
use Data\Access\Tables\SuspensionTypeDAO as SuspensionTypeDAO;
use Data\Access\Tables\SuspensionReasonDAO as SuspensionReasonDAO;
use Data\Access\Tables\SuspensionObjectDAO as SuspensionObjectDAO;
use Data\Access\Tables\SuspensionDAO as SuspensionDAO;
use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Tables\DocumentUserDAO as DocumentUserDAO;
use Data\Access\Tables\SuspensionArticleDAO as SuspensionArticleDAO;
use Data\Access\Tables\SuspensionDecisionDAO as SuspensionDecisionDAO;
use Data\Access\Tables\SuspensionTicketDAO as SuspensionTicketDAO;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Data\Access\Views\SuspensionArticleDetailsView as SuspensionArticleDetailsView;
use Data\Access\Views\SuspensionDecisionDetailsView as SuspensionDecisionDetailsView;
use Data\Access\Views\SuspensionTicketDetailsView as SuspensionTicketDetailsView;
use Data\Exceptions\NotFoundException as NotFoundException;
use Tanweb\Container as Container;
use Tanweb\Session as Session;


/**
 * Description of SuspensionService
 *
 * @author Tanzar
 */
class SuspensionService {
    private SuspensionGroupDAO $suspensionGroup;
    private SuspensionTypeDAO $suspensionType;
    private SuspensionReasonDAO $suspensionReason;
    private SuspensionObjectDAO $suspensionObject;
    private SuspensionDAO $suspension;
    private DocumentDAO $document;
    private DocumentUserDAO $documentUser;
    private SuspensionArticleDAO $suspensionArticle;
    private SuspensionDecisionDAO $suspensionDecision;
    private SuspensionTicketDAO $suspensionTicket;
    private SuspensionDetailsView $suspensionDetails;
    private UsersWithoutPasswordsView $users;
    private SuspensionArticleDetailsView $suspensionArticleDetails;
    private SuspensionDecisionDetailsView $suspensionDecisionDetails;
    private SuspensionTicketDetailsView $suspensionTicketDetails;
    
    public function __construct() {
        $this->suspensionGroup = new SuspensionGroupDAO();
        $this->suspensionType = new SuspensionTypeDAO();
        $this->suspensionReason = new SuspensionReasonDAO();
        $this->suspensionObject = new SuspensionObjectDAO();
        $this->suspension = new SuspensionDAO();
        $this->document = new DocumentDAO();
        $this->documentUser = new DocumentUserDAO();
        $this->suspensionArticle = new SuspensionArticleDAO();
        $this->suspensionDecision = new SuspensionDecisionDAO();
        $this->suspensionTicket = new SuspensionTicketDAO();
        $this->suspensionDetails = new SuspensionDetailsView();
        $this->users = new UsersWithoutPasswordsView();
        $this->suspensionArticleDetails = new SuspensionArticleDetailsView();
        $this->suspensionDecisionDetails = new SuspensionDecisionDetailsView();
        $this->suspensionTicketDetails = new SuspensionTicketDetailsView();
    }
    
    public function getAllTypes() : Container {
        return $this->suspensionType->getAll();
    }
    
    public function getAllTypesByGroupId(int $id) : Container {
        return $this->suspensionType->getByGroupId($id);
    }
    
    public function getActiveTypesByGroupId(int $id) : Container {
        return $this->suspensionType->getActiveByGroupId($id);
    }
    
    public function getAllGroups() : Container {
        return $this->suspensionGroup->getAll();
    }
    
    public function getActiveGroups() : Container {
        return $this->suspensionGroup->getActive();
    }
    
    public function getAllReasons() : Container {
        return $this->suspensionReason->getAll();
    }
    
    public function getAllObjects() : Container {
        return $this->suspensionObject->getAll();
    }
    
    public function getCurrentUserSuspensions(int $idDocument) : Container {
        $username = Session::getUsername();
        return $this->suspensionDetails->getActiveByUsernameAndIdDocument($username, $idDocument);
    }
    
    public function getSuspensionDetails(int $idDocument) : Container {
        $groups = $this->suspensionGroup->getActive();
        $types = $this->suspensionType->getActive();
        $objects = $this->suspensionObject->getActive();
        $reasons = $this->suspensionReason->getActive();
        $document = $this->document->getById($idDocument);
        $result = new Container();
        $result->add($groups->toArray(), 'groups');
        $result->add($types->toArray(), 'types');
        $result->add($objects->toArray(), 'objects');
        $result->add($reasons->toArray(), 'reasons');
        $result->add($document->get('start'), 'start');
        $result->add($document->get('end'), 'end');
        return $result;
    }
    
    public function getAssignedUserArticles(string $username, int $idSuspension) : Container {
        return $this->suspensionArticleDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function getAssignedCurrentUserArticles(int $idSuspension) : Container {
        $username = Session::getUsername();
        return $this->suspensionArticleDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function getAssignedUserTickets(string $username, int $idSuspension) : Container {
        return $this->suspensionTicketDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function getAssignedCurrentUserTickets(int $idSuspension) : Container {
        $username = Session::getUsername();
        return $this->suspensionTicketDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function getAssignedUserDecisions(string $username, int $idSuspension) : Container {
        return $this->suspensionDecisionDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function getAssignedCurrentUserDecisions(int $idSuspension) : Container {
        $username = Session::getUsername();
        return $this->suspensionDecisionDetails->getActiveByUsernameAndIdSuspension($username, $idSuspension);
    }
    
    public function saveType(Container $data) : int{
        return $this->suspensionType->save($data);
    }
    
    public function saveGroup(Container $data) : int{
        return $this->suspensionGroup->save($data);
    }
    
    public function saveReason(Container $data) : int{
        return $this->suspensionReason->save($data);
    }
    
    public function saveObject(Container $data) : int {
        return $this->suspensionObject->save($data);
    }
    
    public function saveSuspensionForCurrentUser(Container $data) : int {
        $documentId = $data->get('id_document');
        $username = Session::getUsername();
        $userDocumentId = (int) $this->getUserDocumentId($documentId, $username);
        $data->add($userDocumentId, 'id_document_user');
        $suspension = $this->parseSuspension($data);
        return $this->suspension->save($suspension);
    }
    
    
    private function getUserDocumentId(int $documentId, string $username) : int {
        $user = $this->users->getByUsername($username);
        $userId = (int) $user->get('id');
        $relation = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        return (int) $relation->get('id');
    }
    
    
    public function saveSuspension(Container $data) : int {
        $suspension = $this->parseSuspension($data);
        return $this->suspension->save($suspension);
    }
    
    private function parseSuspension(Container $data) : Container {
        $suspension = new Container();
        if($data->isValueSet('id')){
            $suspension->add($data->get('id'), 'id');
        }
        if($data->isValueSet('active')){
            $suspension->add($data->get('active'), 'active');
        }
        $suspension->add($data->get('date'), 'date');
        $suspension->add($data->get('shift'), 'shift');
        $suspension->add($data->get('region'), 'region');
        $suspension->add($data->get('description'), 'description');
        $suspension->add($data->get('correction_date'), 'correction_date');
        $suspension->add($data->get('correction_shift'), 'correction_shift');
        $suspension->add($data->get('remarks'), 'remarks');
        $suspension->add($data->get('external_company'), 'external_company');
        $suspension->add($data->get('company_name'), 'company_name');
        $suspension->add($data->get('id_suspension_type'), 'id_suspension_type');
        $suspension->add($data->get('id_suspension_object'), 'id_suspension_object');
        $suspension->add($data->get('id_suspension_reason'), 'id_suspension_reason');
        $suspension->add($data->get('id_document_user'), 'id_document_user');
        return $suspension;
    }
    
    public function assignArticle(int $idSuspension, int $idArticle) : int {
        try{
            $relation = $this->suspensionArticle->getBySuspensionAndArticle($idSuspension, $idArticle);
            return (int) $relation->get('id');
        } catch (NotFoundException $ex) {
            $relation = new Container();
            $relation->add($idArticle, 'id_art_41');
            $relation->add($idSuspension, 'id_suspension');
            return $this->suspensionArticle->save($relation);
        }
    }
    
    public function unassignArticle(int $idSuspension, int $idArticle) : void {
        try{
            $relation = $this->suspensionArticle->getBySuspensionAndArticle($idSuspension, $idArticle);
            $id = (int) $relation->get('id');
            $this->suspensionArticle->remove($id);
        } catch (NotFoundException $ex) {
        }
    }
    
    public function assignTicket(int $idSuspension, int $idTicket) : int {
        try{
            $relation = $this->suspensionTicket->getBySuspensionAndTicket($idSuspension, $idTicket);
            return (int) $relation->get('id');
        } catch (NotFoundException $ex) {
            $relation = new Container();
            $relation->add($idTicket, 'id_ticket');
            $relation->add($idSuspension, 'id_suspension');
            return $this->suspensionTicket->save($relation);
        }
    }
    
    public function unassignTicket(int $idSuspension, int $idTicket) : void {
        try{
            $relation = $this->suspensionTicket->getBySuspensionAndTicket($idSuspension, $idTicket);
            $id = (int) $relation->get('id');
            $this->suspensionTicket->remove($id);
        } catch (NotFoundException $ex) {
        }
    }
    
    public function assignDecision(int $idSuspension, int $idDecision) : int {
        try{
            $relation = $this->suspensionDecision->getBySuspensionAndDecision($idSuspension, $idDecision);
            return (int) $relation->get('id');
        } catch (NotFoundException $ex) {
            $relation = new Container();
            $relation->add($idDecision, 'id_decision');
            $relation->add($idSuspension, 'id_suspension');
            return $this->suspensionDecision->save($relation);
        }
    }
    
    public function unassignDecision(int $idSuspension, int $idDecision) : void {
        try{
            $relation = $this->suspensionDecision->getBySuspensionAndDecision($idSuspension, $idDecision);
            $id = (int) $relation->get('id');
            $this->suspensionDecision->remove($id);
        } catch (NotFoundException $ex) {
        }
    }
    
    public function changeTypeStatus(int $id) : void {
        $item = $this->suspensionType->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionType->disable($id);
        }
        else{
            $this->suspensionType->enable($id);
        }
    }
    
    public function changeGroupStatus(int $id) : void {
        $item = $this->suspensionGroup->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionGroup->disable($id);
        }
        else{
            $this->suspensionGroup->enable($id);
        }
    }
    
    public function changeReasonStatus(int $id) : void {
        $item = $this->suspensionReason->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionReason->disable($id);
        }
        else{
            $this->suspensionReason->enable($id);
        }
    }
    
    public function changeObjectStatus(int $id) : void {
        $item = $this->suspensionObject->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionObject->disable($id);
        }
        else{
            $this->suspensionObject->enable($id);
        }
    }
    
    public function changeSuspensionStatus(int $id) : void {
        $item = $this->suspension->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspension->disable($id);
        }
        else{
            $this->suspension->enable($id);
        }
    }
    
    public function disableSuspesnion(int $id) : void {
        $this->suspension->disable($id);
    }
}

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
use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Tables\SuspensionArticleDAO as SuspensionArticleDAO;
use Data\Access\Tables\SuspensionDecisionDAO as SuspensionDecisionDAO;
use Data\Access\Tables\SuspensionTicketDAO as SuspensionTicketDAO;
use Data\Access\Tables\SuspensionTypeObjectDAO as SuspensionTypeObjectDAO;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Data\Access\Views\SuspensionArticleDetailsView as SuspensionArticleDetailsView;
use Data\Access\Views\SuspensionDecisionDetailsView as SuspensionDecisionDetailsView;
use Data\Access\Views\SuspensionTicketDetailsView as SuspensionTicketDetailsView;
use Data\Access\Views\SuspensionTypeObjectDetailsView as SuspensionTypeObjectDetailsView;
use Data\Access\Views\ArticleDetailsView as ArticleDetailsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Data\Exceptions\NotFoundException as NotFoundException;
use Services\Exceptions\SystemBlockedException as SystemBlockedException;
use Services\Exceptions\IncorrectShiftsDatesException as IncorrectShiftsDatesException;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Config\INI\Languages as Languages;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;
use Custom\Parsers\Database\Suspension as Suspension;
use DateTime;

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
    private SuspensionTypeObjectDAO $suspensionTypeObject;
    private SuspensionDetailsView $suspensionDetails;
    private UsersWithoutPasswordsView $users;
    private SuspensionArticleDetailsView $suspensionArticleDetails;
    private SuspensionDecisionDetailsView $suspensionDecisionDetails;
    private SuspensionTicketDetailsView $suspensionTicketDetails;
    private SuspensionTypeObjectDetailsView $suspensionTypeObjectDetails;
    private ArticleDetailsView $articleDetails;
    private DecisionDetailsView $decisionDetails;
    private TicketDetailsView $ticketDetails;
    
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
        $this->suspensionTypeObject = new SuspensionTypeObjectDAO();
        $this->suspensionDetails = new SuspensionDetailsView();
        $this->users = new UsersWithoutPasswordsView();
        $this->suspensionArticleDetails = new SuspensionArticleDetailsView();
        $this->suspensionDecisionDetails = new SuspensionDecisionDetailsView();
        $this->suspensionTicketDetails = new SuspensionTicketDetailsView();
        $this->suspensionTypeObjectDetails = new SuspensionTypeObjectDetailsView();
        $this->articleDetails = new ArticleDetailsView();
        $this->decisionDetails = new DecisionDetailsView();
        $this->ticketDetails = new TicketDetailsView();
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
    
    public function getObjectsByType(int $idType) : Container {
        return $this->suspensionTypeObjectDetails->getActiveObjectsByType($idType);
    }
    
    public function getCurrentUserSuspensions(int $idDocument) : Container {
        $username = Session::getUsername();
        return $this->suspensionDetails->getActiveByUsernameAndIdDocument($username, $idDocument);
    }
    
    public function getCurrentUserActiveSuspensionsByYear(int $year) : Container {
        $username = Session::getUsername();
        return $this->suspensionDetails->getActiveByUsernameAndYear($username, $year);
    }
    
    public function getActiveSuspensionsByMonthAndYear(int $month, int $year) : Container {
        return $this->suspensionDetails->getActiveByMonthAndYear($month, $year);
    }
    
    public function getActiveSuspensionsByMonthAndYearWithDecisionInfo(int $month, int $year) : Container {
        $suspensions = $this->suspensionDetails->getActiveByMonthAndYear($month, $year);
        $this->addDecisionInfo($suspensions);
        return $suspensions;
    }
    
    private function addDecisionInfo(Container $suspensions) : void {
        $suspensionsWithDecisions = $this->suspensionDecisionDetails->getAll();
        foreach ($suspensions->toArray() as $key => $suspension){
            foreach ($suspensionsWithDecisions->toArray() as $decision){
                if($suspension['id'] === $decision['id_suspension']){
                    $suspension['decision_text'] = 'Tak';
                    break;
                }
            }
            if(!isset($suspension['decision_text'])){
                $suspension['decision_text'] = 'Nie';
            }
            $suspensions->add($suspension, $key, true);
        }
    }
    
    public function getSuspensionDetails(int $idDocument) : Container {
        $groups = $this->suspensionGroup->getActive();
        $types = $this->suspensionType->getActive();
        $objects = $this->suspensionObject->getActive();
        $reasons = $this->suspensionReason->getActive();
        $typeObjectsRelations = $this->suspensionTypeObjectDetails->getActive();
        $document = $this->document->getById($idDocument);
        $result = new Container();
        $result->add($groups->toArray(), 'groups');
        $result->add($types->toArray(), 'types');
        $result->add($objects->toArray(), 'objects');
        $result->add($reasons->toArray(), 'reasons');
        $result->add($typeObjectsRelations->toArray(), 'typeObjectRelations');
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
    
    public function getAllUserSuspensions(string $username, int $year) : Container {
        return $this->suspensionDetails->getAllByUsernameAndYear($username, $year);
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
        $this->checkBlocker($data);
        $documentId = $data->get('id_document');
        $username = Session::getUsername();
        $userDocumentId = (int) $this->getUserDocumentId($documentId, $username);
        $data->add($userDocumentId, 'id_document_user', true);
        $parser = new Suspension();
        $suspension = $parser->parse($data);
        $this->checkSuspensionShifts($suspension);
        return $this->suspension->save($suspension);
    }
    
    private function getUserDocumentId(int $documentId, string $username) : int {
        $user = $this->users->getByUsername($username);
        $userId = (int) $user->get('id');
        $relation = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        return (int) $relation->get('id');
    }
    
    
    public function saveSuspension(Container $data) : int {
        $this->checkBlocker($data);
        $parser = new Suspension();
        $suspension = $parser->parse($data);
        $this->checkSuspensionShifts($suspension);
        return $this->suspension->save($suspension);
    }
    
    public function saveTypeObjects(int $idType, Container $objectIds) : void {
        $relations = $this->suspensionTypeObject->getByType($idType);
        foreach ($objectIds->toArray() as $idObject){
            foreach ($relations->toArray() as $index => $item){
                $relation = new Container($item);
                if((int) $relation->get('id_suspension_object') === (int) $idObject){
                    $relations->remove($index);
                }
            }
        }
        foreach ($objectIds->toArray() as $idObject){
            $this->saveTypeObject($idType, $idObject);
        }
        foreach ($relations->toArray() as $item){
            $relation = new Container($item);
            $id = (int) $relation->get('id');
            $this->suspensionTypeObject->remove($id);
        }
        $k = 0;
    }
    
    private function saveTypeObject(int $idType, int $idObject) : int {
        $relation = $this->suspensionTypeObject->getByTypeAndObject($idType, $idObject);
        if($relation->isEmpty()){
            $data = new Container();
            $data->add($idType, 'id_suspension_type');
            $data->add($idObject, 'id_suspension_object');
            $id = (int) $this->suspensionTypeObject->save($data);
        }
        else{
            $id = (int) $relation->get('id');
        }
        return $id;
    }
    
    
    public function assignArticle(int $idSuspension, int $idArticle) : int {
        try{
            $this->checkArticleBlocker($idArticle);
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
            $this->checkArticleBlocker($idArticle);
            $relation = $this->suspensionArticle->getBySuspensionAndArticle($idSuspension, $idArticle);
            $id = (int) $relation->get('id');
            $this->suspensionArticle->remove($id);
        } catch (NotFoundException $ex) {
        }
    }
    
    public function assignTicket(int $idSuspension, int $idTicket) : int {
        try{
            $this->checkTicketBlocker($idTicket);
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
            $this->checkTicketBlocker($idTicket);
            $relation = $this->suspensionTicket->getBySuspensionAndTicket($idSuspension, $idTicket);
            $id = (int) $relation->get('id');
            $this->suspensionTicket->remove($id);
        } catch (NotFoundException $ex) {
        }
    }
    
    public function assignDecision(int $idSuspension, int $idDecision) : int {
        try{
            $this->checkDecisionBlocker($idDecision);
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
            $this->checkDecisionBlocker($idDecision);
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
        $suspension = $this->suspension->getById($id);
        $this->checkBlocker($suspension);
        $this->suspension->disable($id);
    }
    
    private function checkBlocker(Container $data) : void {
        $blocker = new InspectorDateBlocker();
        if($data->isValueSet('id')){
            $dataToCheck = $this->suspension->getById($data->get('id'));
        }
        else{
            $dataToCheck = $data;
        }
        if($blocker->isBLocked($dataToCheck)){
            throw new SystemBlockedException();
        }
    }
    
    private function checkArticleBlocker(int $articleId) : void {
        $data = $this->articleDetails->getById($articleId);
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            throw new SystemBlockedException();
        }
    }
    
    private function checkDecisionBlocker(int $decisionId) : void {
        $data = $this->decisionDetails->getById($decisionId);
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            throw new SystemBlockedException();
        }
    }
    
    private function checkTicketBlocker(int $ticketId) : void {
        $data = $this->ticketDetails->getById($ticketId);
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            throw new SystemBlockedException();
        }
    }
    
    private function checkSuspensionShifts(Container $suspension) : void {
        $date = new DateTime($suspension->get('date'));
        $correctionDate = new DateTime($suspension->get('correction_date'));
        if($date > $correctionDate){
            throw new IncorrectShiftsDatesException();
        }
    }
}

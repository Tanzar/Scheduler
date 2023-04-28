<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\TicketDAO as TicketDAO;
use Data\Access\Tables\TicketLawDAO as TicketLawDAO;
use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Tables\PositionGroupsDAO as PositionGroupsDAO;
use Data\Access\Tables\DocumentUserDAO as DocumentUserDAO;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;
use Custom\Parsers\Database\Ticket as Ticket;
use Services\Exceptions\SystemBlockedException as SystemBlockedException;

/**
 * Description of TicketService
 *
 * @author Tanzar
 */
class TicketService {
    private TicketDAO $ticket;
    private TicketLawDAO $ticketLaw;
    private DocumentDAO $document;
    private PositionGroupsDAO $positionGroups;
    private DocumentUserDAO $documentUser;
    private TicketDetailsView $ticketDetails;
    private UsersWithoutPasswordsView $users;
    
    public function __construct() {
        $this->ticket = new TicketDAO();
        $this->ticketLaw = new TicketLawDAO();
        $this->document = new DocumentDAO();
        $this->positionGroups = new PositionGroupsDAO();
        $this->documentUser = new DocumentUserDAO();
        $this->ticketDetails = new TicketDetailsView();
        $this->users = new UsersWithoutPasswordsView();
    }
    
    public function getAllUserTicketsByYear(string $username, int $year) : Container {
        return $this->ticketDetails->getAllUserTicketsByYear($username, $year);
    }
    
    public function getCurrentUserActiveTicketsByDocument(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->ticketDetails->getUserActiveTicketsByDocumentId($username, $documentId);
    }
    
    public function getCurrentUserActiveTicketsByYear(int $year) : Container {
        $username = Session::getUsername();
        return $this->ticketDetails->getActiveUserTicketsByYear($username, $year);
    }
    
    public function getActiveTicketsByMonthAndYear(int $month, int $year) : Container {
        return $this->ticketDetails->getActiveByMonthAndYear($month, $year);
    }
    
    public function getById(int $id) : Container {
        return $this->ticketDetails->getById($id);
    }
    
    public function getAllTicketLaws() : Container {
        return $this->ticketLaw->getAll();
    }
    
    public function getNewTicketDetails(int $documentId) : Container {
        $document = $this->document->getById($documentId);
        $start = $document->get('start');
        $end = $document->get('end');
        $laws = $this->ticketLaw->getActive();
        $groups = $this->positionGroups->getActive();
        $result = new Container();
        $result->add($start, 'start');
        $result->add($end, 'end');
        $result->add($laws->toArray(), 'ticket_laws');
        $result->add($groups->toArray(), 'position_groups');
        return $result;
    }
    
    public function saveTicketForCurrentUser(Container $data) : int {
        $this->checkBlocker($data);
        $documentId = (int) $data->get('id_document');
        $data->remove('id_document');
        $username = Session::getUsername();
        $documentUserId = $this->getUserDocumentId($documentId, $username);
        $data->add($documentUserId, 'id_document_user');
        return $this->ticket->save($data);
    }
    
    private function getUserDocumentId(int $documentId, string $username) : int {
        $user = $this->users->getByUsername($username);
        $userId = (int) $user->get('id');
        $relation = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        return (int) $relation->get('id');
    }
    
    public function updateTicket(Container $data) : void {
        $this->checkBlocker($data);
        $parser = new Ticket();
        $ticket = $parser->parse($data);
        $this->ticket->save($ticket);
    }
    
    public function saveTicketLaw(Container $data) : int {
        return $this->ticketLaw->save($data);
    }
    
    public function changeTicketLawStatus(int $id) : void {
        $law = $this->ticketLaw->getById($id);
        $active = $law->get('active');
        if($active){
            $this->ticketLaw->disable($id);
        }
        else{
            $this->ticketLaw->enable($id);
        }
    }
    
    public function changeTicketStatus(int $id) : void {
        $ticket = $this->ticket->getById($id);
        $active = $ticket->get('active');
        if($active){
            $this->ticket->disable($id);
        }
        else{
            $this->ticket->enable($id);
        }
    }
    
    public function disableTicket(int $id) {
        $data = $this->ticket->getById($id);
        $this->checkBlocker($data);
        $this->ticket->disable($id);
    }
    
    public function enableTicket(int $id) {
        $data = $this->ticket->getById($id);
        $this->checkBlocker($data);
        $this->ticket->enable($id);
    }
    
    private function checkBlocker(Container $data) {
        $blocker = new InspectorDateBlocker();
        if($data->isValueSet('id')){
            $dataToCheck = $this->ticket->getById($data->get('id'));
        }
        else{
            $dataToCheck = $data;
        }
        if($blocker->isBLocked($dataToCheck)){
            throw new SystemBlockedException();
        }
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\TicketDAO as TicketDAO;
use Data\Access\Tables\TicketLawDAO as TicketLawDAO;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;

/**
 * Description of TicketService
 *
 * @author Tanzar
 */
class TicketService {
    private TicketDAO $ticket;
    private TicketLawDAO $ticketLaw;
    private TicketDetailsView $ticketDetails;
    
    public function __construct() {
        $this->ticket = new TicketDAO();
        $this->ticketLaw = new TicketLawDAO();
        $this->ticketDetails = new TicketDetailsView();
    }
    
    public function getAllUserTicketsByDocument(string $username, int $documentId) : Container {
        return $this->ticketDetails->getAllUserTicketsByDocumentId($username, $documentId);
    }
    
    public function getCurrentUserActiveTicketsByDocument(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->ticketDetails->getUserActiveTicketsByDocumentId($username, $documentId);
    }
    
    public function saveTicketForCurrentUser(Container $data){
        
    }
    
    private function parseTicket(Container $data) : Container{
        $ticket = new Container();
        
    }
}

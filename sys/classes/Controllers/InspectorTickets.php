<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Services\TicketService as TicketService;
use Services\DocumentService as DocumentService;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectorTickets
 *
 * @author Tanzar
 */
class InspectorTickets extends Controller{
    private DocumentService $documentService;
    private TicketService $ticketService;
    
    public function __construct() {
        $this->documentService = new DocumentService();
        $this->ticketService = new TicketService();
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
    
    public function getTickets() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->ticketService->getCurrentUserActiveTicketsByDocument($id);
        $this->setResponse($response);
    }
    
    public function getNewTicketDetails() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->ticketService->getNewTicketDetails($id);
        $this->setResponse($response);
    }
    
    public function saveNewTicket() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->ticketService->saveTicketForCurrentUser($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function updateTicket() {
        $data = $this->getRequestData();
        $languages = Languages::getInstance();
        $id = $this->ticketService->updateTicket($data);
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeTicket() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $languages = Languages::getInstance();
        $this->ticketService->disableTicket($id);
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

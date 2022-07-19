<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\TicketService as TicketService;
use Services\PositionGroupsService as PositionGroupsService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelTicket
 *
 * @author Tanzar
 */
class AdminPanelTicket extends Controller{
    private TicketService $ticketService;
    private PositionGroupsService $positionGroupsService;
    
    public function __construct() {
        $this->ticketService = new TicketService();
        $this->positionGroupsService = new PositionGroupsService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getUserTickets() {
        $data = $this->getRequestData();
        $username = $data->get('username');
        $year = (int) $data->get('year');
        $response = $this->ticketService->getAllUserTicketsByYear($username, $year);
        $this->setResponse($response);
    }
    
    public function getAllTicketLaws() {
        $response = $this->ticketService->getAllTicketLaws();
        $this->setResponse($response);
    }
    
    public function getAllPositionGroups() {
        $response = $this->positionGroupsService->getAll();
        $this->setResponse($response);
    }
    
    public function saveTicketLaw() {
        $data = $this->getRequestData();
        $id = $this->ticketService->saveTicketLaw($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function savePositionGroup() {
        $data = $this->getRequestData();
        $id = $this->positionGroupsService->save($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeTicketLawStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->ticketService->changeTicketLawStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeTicketStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->ticketService->changeTicketStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changePositionGroupStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->positionGroupsService->changeStatus($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

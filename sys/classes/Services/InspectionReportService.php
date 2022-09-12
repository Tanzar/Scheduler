<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Data\Access\Views\DocumentEntriesDetailsView as DocumentEntriesDetailsView;
use Data\Access\Views\TicketDetailsView as TicketDetailsView;
use Data\Access\Views\ArticleDetailsView as ArticleDetalsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;

/**
 * Description of InspectionReport
 *
 * @author Tanzar
 */
class InspectionReportService {
    private DocumentDetailsView $documentDetails;
    private DocumentUserDetailsView $documentUserDetails;
    private DocumentEntriesDetailsView $documentEntriesDetails;
    private TicketDetailsView $ticketDetails;
    private ArticleDetalsView $articleDetails;
    private DecisionDetailsView $decisionDetails;


    public function __construct() {
        $this->documentDetails = new DocumentDetailsView();
        $this->documentUserDetails = new DocumentUserDetailsView();
        $this->documentEntriesDetails = new DocumentEntriesDetailsView();
        $this->ticketDetails = new TicketDetailsView();
        $this->articleDetails = new ArticleDetalsView();
        $this->decisionDetails = new DecisionDetailsView();
    }
    
    public function generateReport(int $documentId, string $username) : Container {
        $report = new Container();
        $report->add($this->getDocumentDetails($documentId), 'details');
        $report->add($this->getAssignedUsers($documentId), 'users');
        $report->add($this->getUserEntriesForDocument($documentId, $username), 'entries');
        $report->add($this->getUserTicketsForDocument($documentId, $username), 'tickets');
        $report->add($this->getUserArticlesForDocument($documentId, $username), 'art_41');
        $report->add($this->getUserDecisionsForDocument($documentId, $username), 'decisions');
        return $report;
    }
    
    private function getDocumentDetails(int $documentId) : array {
        $doc = $this->documentDetails->getById($documentId);
        return $doc->toArray();
    }
    
    private function getAssignedUsers(int $documentId) : array {
        $data = $this->documentUserDetails->getActiveByDocumentId($documentId);
        $result = array();
        foreach($data->toArray() as $item){
            $user = new Container($item);
            $name = $user->get('name') . ' ' . $user->get('surname');
            $result[] = $name;
        }
        return $result;
    }
    
    private function getUserEntriesForDocument(int $documentId, string $username) : array {
        $data = $this->documentEntriesDetails->getActiveByUsernameAndDocumentId($username, $documentId);
        $languages = Languages::getInstance();
        $interface = new Container($languages->get('interface'));
        $result = array();
        foreach ($data->toArray() as $item){
            $entry = new Container($item);
            $value = $this->parseEntryToString($entry, $interface);
            $result[] = $value;
        }
        return $result;
    }
    
    private function parseEntryToString(Container $entry, Container $interface) : string {
        $start = DateTime::createFromFormat('Y-m-d H:i:s', $entry->get('start'));
        $end = DateTime::createFromFormat('Y-m-d H:i:s', $entry->get('end'));
        $value = $start->format('H:i d-m-Y') . ' - ' . 
                $end->format('H:i d-m-Y') . ' :  ' . $entry->get('activity') . ' ';
        $underground = (bool) $entry->get('underground');
        if($underground){
            $value .= $interface->get('underground');
        }
        else{
            $value .= $interface->get('surface');
        }
        return $value;
    }
    
    private function getUserTicketsForDocument(int $documentId, string $username) : array {
        $data = $this->ticketDetails->getUserActiveTicketsByDocumentId($username, $documentId);
        $result = array();
        foreach ($data->toArray() as $item){
            $ticket = new Container($item);
            $text = '' . $ticket->get('date') . ' : ' . $ticket->get('number')
                    . ': ' . $ticket->get('violated_rules');
            $result[] = $text;
        }
        return $result;
    }
    
    private function getUserArticlesForDocument(int $documentId, string $username) : array {
        $data = $this->articleDetails->getUserActiveArticlesByDocumentId($username, $documentId);
        $result = array();
        foreach ($data->toArray() as $item){
            $ticket = new Container($item);
            $text = '' . $ticket->get('date') . ' : ' . $ticket->get('art_41_form_short')
                    . ': ' . $ticket->get('position');
            $result[] = $text;
        }
        return $result;
    }
    
    private function getUserDecisionsForDocument(int $documentId, string $username) : array {
        $data = $this->decisionDetails->getActiveByUsernameAndDocumentId($username, $documentId);
        $result = array();
        foreach ($data->toArray() as $item){
            $decision = new Container($item);
            $text = '' . $decision->get('date') . ' : ' . $decision->get('law');
            $result[] = $text;
        }
        return $result;
    }
}

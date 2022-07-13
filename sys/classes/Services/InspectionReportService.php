<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Data\Access\Views\DocumentEntriesDetailsView as DocumentEntriesDetailsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of InspectionReport
 *
 * @author Tanzar
 */
class InspectionReportService {
    private DocumentDAO $document;
    private DocumentUserDetailsView $documentUserDetails;
    private DocumentEntriesDetailsView $documentEntriesDetails;
    
    public function __construct() {
        $this->document = new DocumentDAO();
        $this->documentUserDetails = new DocumentUserDetailsView();
        $this->documentEntriesDetails = new DocumentEntriesDetailsView();
    }
    
    public function generateReport(int $documentId, string $username) : Container {
        $report = new Container();
        $report->add($this->getDocumentDetails($documentId), 'details');
        $report->add($this->getAssignedUsers($documentId), 'users');
        $report->add($this->getUserEntriesForDocument($documentId, $username), 'entries');
        return $report;
    }
    
    private function getDocumentDetails(int $documentId) : array {
        $doc = $this->document->getById($documentId);
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
            $value = $entry->get('start') . ' - ' . $entry->get('end') . '   ' .
                    $entry->get('location') . ' : ' . $entry->get('activity') . ' ';
            $underground = (bool) $entry->get('underground');
            if($underground){
                $value .= $interface->get('underground');
            }
            else{
                $value .= $interface->get('surface');
            }
            $result[] = $value;
        }
        return $result;
    }
}

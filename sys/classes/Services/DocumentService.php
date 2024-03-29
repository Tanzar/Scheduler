<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Custom\Parsers\Database\Document as Document;
use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Tables\DocumentUserDAO as DocumentUserDAO;
use Data\Access\Tables\DocumentScheduleDAO as DocumentScheduleDAO;
use Data\Access\Tables\ScheduleTableDAO as ScheduleTableDAO;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Data\Access\Views\DocumentEntriesDetailsView as DocumentEntriesDetailsView;
use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Tables\UserTableDAO as UserDAO;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;
use Tanweb\Session as Session;


/**
 * Description of DocumentService
 *
 * @author Tanzar
 */
class DocumentService {
    private DocumentDAO $document;
    private DocumentUserDAO $documentUser;
    private DocumentScheduleDAO $documentSchedule;
    private ScheduleTableDAO $schedule;
    private DocumentUserDetailsView $documentUserDetails;
    private DocumentEntriesDetailsView $documentEntriesDetails;
    private DocumentDetailsView $documentDetails;
    private UserDAO $user;
    
    public function __construct() {
        $this->document = new DocumentDAO();
        $this->documentUser = new DocumentUserDAO();
        $this->documentSchedule = new DocumentScheduleDAO();
        $this->schedule = new ScheduleTableDAO();
        $this->documentUserDetails = new DocumentUserDetailsView();
        $this->documentEntriesDetails = new DocumentEntriesDetailsView();
        $this->documentDetails = new DocumentDetailsView();
        $this->user = new UserDAO();
    }
    
    public function getDocumentsByMonthYear(int $month, int $year) : Container{
        return $this->documentDetails->getActiveByMonthAndYear($month, $year);
    }
    
    public function getAllDocumentsByMonthYear(int $month, int $year) : Container{
        return $this->documentDetails->getAllByMonthAndYear($month, $year);
    }
    
    public function getDocumentsByMonthYearUsername(int $month, int $year, string $username) : Container {
        return $this->documentUserDetails->getActiveByMonthYearUsername($month, $year, $username);
    }
    
    public function getCurrentUserDocumentsByMonthYear(int $month, int $year) : Container {
        $username = Session::getUsername();
        return $this->documentUserDetails->getActiveByMonthYearUsername($month, $year, $username);
    }
    
    public function getCurrentUserDocumentsByYear(int $year) : Container {
        $username = Session::getUsername();
        return $this->documentUserDetails->getActiveDocumentByYearUsername($year, $username);
    }
    
    public function getDocumentsByYear(int $year) : Container {
        return $this->documentDetails->getActiveByYear($year);
    }
    
    public function getDocumentsByUserEntryDetails(Container $entryDetails) : Container{
        if($entryDetails->isValueSet('username')){
            $username = $entryDetails->get('username');
        }
        else{
            $username = Session::getUsername();
        }
        $start = $entryDetails->get('start');
        $end = $entryDetails->get('end');
        $locationId = (int) $entryDetails->get('id_location');
        $startDate = date('Y-m-d', strtotime($start));
        $endDate = date('Y-m-d', strtotime($end));
        return $this->documentUserDetails->getActiveByUsernameLocationIdEntryDates($username, $locationId, $startDate, $endDate);
    }
    
    public function getUsersByDocumentId(int $documentId) : Container {
        return $this->documentUserDetails->getByDocumentId($documentId);
    }
    
    public function getAllDocumentUsersAndEntries(int $documentId) : Container{
        $users = $this->documentUserDetails->getByDocumentId($documentId);
        $entries = $this->documentEntriesDetails->getActiveByDocumentId($documentId);
        $result = new Container();
        $result->add($users->toArray(), 'users');
        $result->add($entries->toArray(), 'entries');
        return $result;
    }
    
    public function saveAndAssignDocument(Container $data) : int {
        $documentId = $this->saveDocument($data);
        $this->assignCurrentUserToDocument($documentId);
        return $documentId;
    }
    
    public function saveDocument(Container $data) : int {
        $parser = new Document();
        $parsed = $parser->parse($data);
        return $this->document->save($parsed);
    }
    
    public function editDocument(Container $data) : string {
        $language = Languages::getInstance();
        $id = (int) $data->get('id');
        $entries = $this->documentEntriesDetails->getActiveByDocumentId($id);
        foreach ($entries->toArray() as $item){
            $entry = new Container($item);
            if($this->isNotInDocumentDaysRange($data, $entry)){
                return $language->get('edit_document_entries_exist_period');
            }
        }
        $parser = new Document();
        $parsed = $parser->parse($data);
        $this->document->save($parsed);
        return $language->get('changes_saved');
    }
    
    private function isNotInDocumentDaysRange(Container $document, Container $entry) : bool {
        $documentStart = $document->get('start');
        $documentEnd = $document->get('end');
        $entryStart = date('Y-m-d', strtotime($entry->get('start')));
        $entryEnd = date('Y-m-d', strtotime($entry->get('end')));
        if($entryEnd >= $documentStart && $entryEnd <= $documentEnd && $entryStart >= $documentStart && $entryStart <= $documentEnd){
            return false;
        }
        else{
            return true;
        }
    }
    
    public function assignCurrentUserToDocument(int $documentId) : int {
        $username = Session::getUsername();
        return $this->assignUserToDocument($username, $documentId);
    }
    
    public function assignUserToDocument(string $username, int $documentId) : int {
        $user = $this->user->getByUsername($username);
        $userId = (int) $user->get('id');
        $assigns = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        if($assigns->length() === 0){
            $item = new Container();
            $item->add($documentId, 'id_document');
            $item->add($userId, 'id_user');
            return $this->documentUser->save($item);
        }
        else{
            return $assigns->get('id');
        }
    }
    
    public function changeDocumentStatus(int $id){
        $documment = $this->document->getById($id);
        $active = $documment->get('active');
        if($active){
            $this->document->disable($id);
        }
        else{
            $this->document->enable($id);
        }
    }
    
    public function unassignUserFromDocument(string $username, int $documentId){
        $user = $this->user->getByUsername($username);
        $userId = (int) $user->get('id');
        $assign = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        if($assign->length() > 0){
            $entries = $this->documentEntriesDetails->getActiveByUsernameAndDocumentId($username, $documentId);
            foreach ($entries->toArray() as $item){
                $entry = new Container($item);
                $id = (int) $entry->get('id');
                $this->schedule->remove($id);
            }
            $id = (int) $assign->get('id');
            $this->documentUser->remove($id);
        }
    }
}

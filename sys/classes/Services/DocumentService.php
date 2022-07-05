<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\DocumentDAO as DocumentDAO;
use Data\Access\Tables\DocumentUserDAO as DocumentUserDAO;
use Data\Access\Tables\DocumentScheduleDAO as DocumentScheduleDAO;
use Data\Access\Views\DocumentUserDetailsDAO as DocumentUserDetailsDAO;
use Data\Access\Views\DocumentEntriesDetailsDAO as DocumentEntriesDetailsDAO;
use Data\Access\Tables\UserDAO as UserDAO;
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
    private DocumentUserDetailsDAO $documentUserDetails;
    private DocumentEntriesDetailsDAO $documentEntriesDetails;
    private UserDAO $user;
    
    public function __construct() {
        $this->document = new DocumentDAO();
        $this->documentUser = new DocumentUserDAO();
        $this->documentSchedule = new DocumentScheduleDAO();
        $this->documentUserDetails = new DocumentUserDetailsDAO();
        $this->documentEntriesDetails = new DocumentEntriesDetailsDAO();
        $this->user = new UserDAO();
    }
    
    public function getDocumentsByMonthYear(int $month, int $year) : Container{
        return $this->document->getActiveByMonthAndYear($month, $year);
    }
    
    public function getAllDocumentsByMonthYear(int $month, int $year) : Container{
        return $this->document->getAllByMonthAndYear($month, $year);
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
    
    public function getDocumentsForUserEntryDates(string $start, string $end) : Container{
        $username = Session::getUsername();
        $startDate = date('Y-m-d', strtotime($start));
        $endDate = date('Y-m-d', strtotime($end));
        return $this->documentUserDetails->getActiveByUsernameAndEntryDates($username, $startDate, $endDate);
    }
    
    public function getUsersByDocumentId(int $documentId) : Container {
        return $this->documentUserDetails->getActiveByDocumentId($documentId);
    }
    
    public function getAllDocumentUsersAndEntries(int $documentId) : Container{
        $users = $this->documentUserDetails->getActiveByDocumentId($documentId);
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
        return $this->document->save($data);
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
            $item = $assigns->get(0);
            $assign = new Container($item);
            $id = (int) $assign->get('id');
            $active = $assign->get('active');
            if(!$active){
                $this->documentUser->enable($id);
            }
            return $id;
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
        $assigns = $this->documentUser->getAllByUserAndDocument($userId, $documentId);
        if($assigns->length() > 0){
            $item = $assigns->get(0);
            $assign = new Container($item);
            $id = (int) $assign->get('id');
            $active = $assign->get('active');
            if($active){
                $this->documentUser->disable($id);
            }
        }
    }
}

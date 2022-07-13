<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\ScheduleDAO as ScheduleDAO;
use Data\Access\Tables\ActivityDAO as ActivityDAO;
use Data\Access\Tables\ActivityLocationTypeDAO as ActivityLocationTypeDAO;
use Data\Access\Tables\UserDAO as UserDAO;
use Data\Access\Tables\DocumentScheduleDAO as DocumentScheduleDAO;
use Data\Access\Views\ActivityLocationTypeDetailsView as ActivityLocationTypeDetailsView;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Tables\LocationDAO as LocationDAO;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Data\Exceptions\NotFoundException as NotFoundException;
use Services\Exceptions\ScheduleEntryException as ScheduleEntryException;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;

/**
 * Description of ScheduleService
 *
 * @author Tanzar
 */
class ScheduleService {
    private ScheduleDAO $schedule;
    private ActivityDAO $activity;
    private ActivityLocationTypeDAO $activityLocation;
    private UserDAO $user;
    private ActivityLocationTypeDetailsView $activityLocationDetails;
    private ScheduleEntriesView $scheduleEntries;
    private DocumentScheduleDAO $documentSchedule;
    private LocationDAO $location;
    private LocationGroupDAO $locationGroup;
    
    public function __construct() {
        $this->schedule = new ScheduleDAO();
        $this->activity = new ActivityDAO();
        $this->activityLocation = new ActivityLocationTypeDAO();
        $this->user = new UserDAO();
        $this->activityLocationDetails = new ActivityLocationTypeDetailsView();
        $this->scheduleEntries = new ScheduleEntriesView();
        $this->documentSchedule = new DocumentScheduleDAO();
        $this->location = new LocationDAO();
        $this->locationGroup = new LocationGroupDAO();
    }
    
    public function getEntries(string $start, string $end) : Container{
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        return $this->scheduleEntries->getActive($startDate, $endDate);
    }
    
    public function getUserEntries(string $username, string $start, string $end) : Container {
        try{
            $user = $this->user->getByUsername($username);
            $userId = $user->get('id');
            $startDate = new DateTime($start);
            $endDate = new DateTime($end);
            return $this->scheduleEntries->getActiveByUserId($userId, $startDate, $endDate);
        }
        catch (NotFoundException $ex){
            return new Container();
        }
    }
    
    public function getAllUserEntries(string $username, string $start, string $end) : Container {
        try{
            $user = $this->user->getByUsername($username);
            $userId = $user->get('id');
            $startDate = new DateTime($start);
            $endDate = new DateTime($end);
            return $this->scheduleEntries->getAllByUserId($userId, $startDate, $endDate);
        }
        catch (NotFoundException $ex){
            return new Container();
        }
    }
    
    public function getEntryByID(int $id) :Container {
        return $this->scheduleEntries->getById($id);
    }
    
    public function getAllActivities() : Container {
        return $this->activity->getAll();
    }
    
    public function getActiveActivities() : Container {
        return $this->activity->getActive();
    }
    
    public function getActivitiesByGroup(string $group) : Container {
        return $this->activity->getByGroup($group);
    }
    
    public function getAllActivityGroups() : Container{
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $activityGroups = $cfg->get('activity_group');
        return new Container($activityGroups);
    }
    
    public function getUserActivityGroups() : Container{
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $activityGroups = $cfg->get('activity_group');
        $groups = new Container();
        $groups->add($activityGroups['delegation'], 'delegation');
        $groups->add($activityGroups['office'], 'office');
        $groups->add($activityGroups['absence'], 'absence');
        $groups->add($activityGroups['other'], 'other');
        return $groups;
    }
    
    public function getCurrentUserEntries(string $start, string $end) : Container {
        $username = Session::getUsername();
        $user = $this->user->getByUsername($username);
        $userId = $user->get('id');
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        return $this->scheduleEntries->getActiveByUserId($userId, $startDate, $endDate);
    }
    
    public function getLocationTypeByIdActivity(int $id) : Container{
        return $this->activityLocationDetails->getByActivityId($id);
    }
    
    public function saveActivity(Container $data) : int {
        return $this->activity->save($data);
    }
    
    public function saveEntryForUser(Container $data) : int {
        $entry = $this->formEntry($data);
        $idSchedule = $this->schedule->save($entry);
        if($data->isValueSet('id_document')){
            $idDocument = (int) $data->get('id_document');
            $this->saveDocumentSchedule($idSchedule, $idDocument);
        }
        return $idSchedule;
    }
    
    private function formEntry(Container $data) : Container {
        $entry = new Container();
        if($data->isValueSet('id')){
            $entry->add($data->get('id'), 'id');
        }
        $idUser = $this->getIdUser($data);
        $entry->add($idUser, 'id_user');
        if($data->isValueSet('id_location')){
            $idLocation = $data->get('id_location');
            $entry->add($idLocation, 'id_location');
        }
        if($data->isValueSet('id_activity')){
            $idActivity = $data->get('id_activity');
            $entry->add($idActivity, 'id_activity');
        }
        if($data->isValueSet('underground')){
            $idUnderground = $data->get('underground');
            $entry->add($idUnderground, 'underground');
        }
        $entry->add($data->get('start'), 'start');
        $entry->add($data->get('end'), 'end');
        $this->checkEntryDates($entry);
        return $entry;
    }
    
    private function getIdUser(Container $data) : int {
        if($data->isValueSet('username')){
            $user = $this->user->getByUsername($data->get('username'));
            return (int) $user->get('id');
        }
        if($data->isValueSet('id_user')){
            return (int) $data->get('id_user');
        }
        else{
            throw new ScheduleEntryException('username and/or id_user not set');
        }
    }
    
    private function saveDocumentSchedule(int $idSchedule, int $idDocument) : void {
        $documentSchedule = new Container();
        $documentSchedule->add($idSchedule, 'id_schedule');
        $documentSchedule->add($idDocument, 'id_document');
        $this->documentSchedule->save($documentSchedule);
    }
    
    public function saveEntryForCurrentUser(Container $data) : int {
        $username = Session::getUsername();
        $data->add($username, 'username');
        $entry = $this->formEntry($data);
        $idSchedule = $this->schedule->save($entry);
        if($data->isValueSet('id_document')){
            $idDocument = (int) $data->get('id_document');
            $this->saveDocumentSchedule($idSchedule, $idDocument);
        }
        return $idSchedule;
    }
    
    public function saveLocation(string $name, int $locationTypeId) : int {
        $item = new Container();
        $item->add($name, 'name');
        $item->add($locationTypeId, 'id_location_type');
        $groupId = $this->getTemporaryGroupId();
        $item->add($groupId, 'id_location_group');
        return $this->location->save($item);
    }
    
    private function getTemporaryGroupId() : int {
        $groups = $this->locationGroup->getByName('tmp');
        if($groups->isEmpty()){
            $item = new Container();
            $item->add('tmp', 'name');
            $item->add(0, 'active');
            return $this->locationGroup->save($item);
        }
        else{
            $item = $groups->get(0);
            $group = new Container($item);
            $id = (int) $group->get('id');
            return $id;
        }
    }
    
    //i'm not sure how to reduce it, maybe later will make something
    public function assign(int $idActivity, Container $locationTypes) : void {
        $oldLocationTypes = $this->activityLocationDetails
                ->getByActivityId($idActivity);
        $idsToAdd = new Container();
        $idsFound = new Container();
        $idsToRemove = new Container();
        foreach($locationTypes->toArray() as $item){
            $new = new Container($item);
            $newId = (int) $new->get('id');
            $found = false;
            foreach ($oldLocationTypes->toArray() as $oldItem){
                $old = new Container($oldItem);
                $oldIdLocationType = (int) $old->get('id_location_type');
                if($newId === $oldIdLocationType){
                    $idsFound->add($old->get('id'));
                    $found = true;
                    break;
                }
            }
            if(!$found){
                $idsToAdd->add($newId);
            }
        }
        
        foreach($oldLocationTypes->toArray() as $oldItem){
            $old = new Container($oldItem);
            $oldId = (int) $old->get('id');
            $found = $idsFound->contains($oldId);
            if(!$found){
                $idsToRemove->add($oldId);
            }
        }
        $this->manageAssign($idActivity, $idsToAdd, $idsToRemove);
    }
    
    public function manageAssign(int $idActivity, Container $idsToAdd, Container $idsToRemove){
        foreach($idsToAdd->toArray() as $idLocationType){
            $item = new Container();
            $item->add($idActivity, 'id_activity');
            $item->add($idLocationType, 'id_location_type');
            $this->activityLocation->save($item);
        }
        
        foreach($idsToRemove->toArray() as $id){
            $this->activityLocation->remove($id);
        }
    }
    
    public function disableEntry(int $id) : void {
        $this->schedule->disable($id);
    }
    
    public function removeActivityLocationType(int $id) : void {
        $this->activityLocation->remove($id);
    }
    
    public function changeActivityStatus(int $id){
        $entry = $this->activity->getByID($id);
        $active = $entry->get('active');
        if($active){
            $this->activity->disable($id);
        }
        else{
            $this->activity->enable($id);
        }
    }
    
    public function changeEntryStatus(int $id){
        $entry = $this->schedule->getByID($id);
        $active = $entry->get('active');
        if($active){
            $this->schedule->disable($id);
            $this->disableDocumentEntryRelation($id);
        }
        else{
            $this->checkEntryDates($entry);
            $this->schedule->enable($id);
            $this->enableDocumentEntryRelation($id);
        }
    }
    
    private function disableDocumentEntryRelation(int $scheduleId) {
        $relations = $this->documentSchedule->getByScheduleId($scheduleId);
        foreach ($relations->toArray() as $item){
            $relation = new Container($item);
            $id = (int) $relation->get('id');
            $this->documentSchedule->disable($id);
        }
    }
    
    private function enableDocumentEntryRelation(int $scheduleId) {
        $relations = $this->documentSchedule->getByScheduleId($scheduleId);
        foreach ($relations->toArray() as $item){
            $relation = new Container($item);
            $id = (int) $relation->get('id');
            $this->documentSchedule->enable($id);
        }
    }
    
    public function changeActivityLocationStatus(int $id){
        $entry = $this->activityLocation->getById($id);
        $active = $entry->get('active');
        if($active){
            $this->activityLocation->disable($id);
        }
        else{
            $this->activityLocation->enable($id);
        }
    }
    
    private function checkEntryDates(Container $data) {
        $start = $data->get('start');
        $end = $data->get('end');
        $languages = Languages::getInstance();
        
        if(strtotime($start) > strtotime($end)){
            throw new ScheduleEntryException($languages->get('start_earlier_than_end'));
        }
        $userId = (int) $data->get('id_user');
        $userEntries = $this->scheduleEntries->getByUserId($userId, new DateTime($start), new DateTime($end));
        if($userEntries->length() > 1){
            throw new ScheduleEntryException($languages->get('entry_for_period_exists'));
        }
        if($userEntries->length() === 1){
            $entry = new Container($userEntries->get(0));
            if(!$data->isValueSet('id') || ((int) $data->get('id')) !== $entry->get('id')){
                throw new ScheduleEntryException($languages->get('entry_for_period_exists'));
            }
        }
    }
    
}

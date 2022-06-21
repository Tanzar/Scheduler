<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\ScheduleDAO as ScheduleDAO;
use Data\Access\Tables\ActivityDAO as ActivityDAO;
use Data\Access\Tables\ActivityLocationTypeDAO as ActivityLocationTypeDAO;
use Data\Access\Tables\UserDAO as UserDAO;
use Data\Access\Views\ActivityLocationTypeDetailsDAO as ActivityLocationTypeDetailsDAO;
use Data\Access\Views\ScheduleEntriesDAO as ScheduleEntriesDAO;
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
    private ActivityLocationTypeDetailsDAO $activityLocationDetails;
    private ScheduleEntriesDAO $scheduleEntries;
    
    public function __construct() {
        $this->schedule = new ScheduleDAO();
        $this->activity = new ActivityDAO();
        $this->activityLocation = new ActivityLocationTypeDAO();
        $this->user = new UserDAO();
        $this->activityLocationDetails = new ActivityLocationTypeDetailsDAO();
        $this->scheduleEntries = new ScheduleEntriesDAO();
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
        if(!$data->isValueSet('id_user')){
            if($data->isValueSet('username')){
                $username = $data->get('username');
                $user = $this->user->getByUsername($username);
                $userId = $user->get('id');
                $data->add($userId, 'id_user');
                $data->remove('username');
            }
            else{
                throw new ScheduleEntryException('username and/or id_user not set');
            }
        }
        $this->checkEntryDates($data);
        return $this->schedule->save($data);
    }
    
    public function saveEntryForCurrentUser(Container $data) : int {
        if(!$data->isValueSet('id_user')){
            $username = Session::getUsername();
            $user = $this->user->getByUsername($username);
            $userId = $user->get('id');
            $data->add($userId, 'id_user');
            $data->remove('username');
        }
        $this->checkEntryDates($data);
        return $this->schedule->save($data);
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
        }
        else{
            $this->checkEntryDates($entry);
            $this->schedule->enable($id);
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

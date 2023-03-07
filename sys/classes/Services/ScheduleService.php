<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\ScheduleTableDAO as ScheduleDAO;
use Data\Access\Tables\ActivityTableDAO as ActivityDAO;
use Data\Access\Tables\ActivityLocationTypeDAO as ActivityLocationTypeDAO;
use Data\Access\Tables\UserTableDAO as UserDAO;
use Data\Access\Tables\DocumentScheduleDAO as DocumentScheduleDAO;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\ActivityLocationTypeDetailsView as ActivityLocationTypeDetailsView;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Tables\LocationDAO as LocationDAO;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Data\Exceptions\NotFoundException as NotFoundException;
use Services\Exceptions\ScheduleEntryException as ScheduleEntryException;
use Services\Exceptions\SystemBlockedException as SystemBlockedException;
use Tanweb\Security\Security as Security;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Tanweb\Config\INI\Languages as Languages;
use Custom\Blockers\ScheduleBlocker as ScheduleBlocker;
use Custom\Parsers\Database\Entry as Entry;
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
    private UsersEmploymentPeriodsView $usersEmploymentPeriods;
    private LocationDAO $location;
    private LocationGroupDAO $locationGroup;
    private Security $secutiry;
    
    public function __construct() {
        $this->schedule = new ScheduleDAO();
        $this->activity = new ActivityDAO();
        $this->activityLocation = new ActivityLocationTypeDAO();
        $this->user = new UserDAO();
        $this->activityLocationDetails = new ActivityLocationTypeDetailsView();
        $this->scheduleEntries = new ScheduleEntriesView();
        $this->documentSchedule = new DocumentScheduleDAO();
        $this->usersEmploymentPeriods = new UsersEmploymentPeriodsView();
        $this->location = new LocationDAO();
        $this->locationGroup = new LocationGroupDAO();
        $this->secutiry = Security::getInstance();
    }
    
    public function getTimetableData(string $start, string $end) : Container {
        $startDate = new DateTime($start . ' 00:00:00');
        $endDate = new DateTime($end . '23:59:59');
        $entries = $this->scheduleEntries->getActive($startDate, $endDate);
        $systemUser = $this->user->getUserByID(1);
        $groups = array();
        $groups[] = array(
                'title' => $systemUser->get('short'),
                'username' => $systemUser->get('username')
            );
        $employedUsers = $this->getVisibleUsers($start, $end);
        foreach ($employedUsers->toArray() as $item) {
            $groups[] = array(
                'title' => $item['short'],
                'username' => $item['username']
            );
        }
        $filteredEntries = $this->filterEntries($entries, $groups);
        $result = new Container();
        $result->add($filteredEntries->toArray(), 'entries');
        $result->add($groups, 'groups');
        return $result;
    }
    
    private function getVisibleUsers(string $start, string $end) : Container {
        $employedUsers =  $this->usersEmploymentPeriods->getOrderedActiveByDatesRange($start, $end);
        $showAllPrivilages = new Container(['admin', 'schedule_admin', 'schedule_show_all']);
        if($this->secutiry->userHaveAnyPrivilage($showAllPrivilages)){
            return $employedUsers;
        }
        else{
            return $this->getVisibleUsersForCurrentUser($employedUsers);
        }
    }
    
    private function getVisibleUsersForCurrentUser(Container $employedUsers) : Container {
        $username = Session::getUsername();
        foreach ($employedUsers->toArray() as $item) {
            $employment = new Container($item);
            if($employment->get('username') === $username){
                $user = $employment;
            }
        }
        $result = new Container();
        if(isset($user)){
            $result->add($user->toArray());
        }
        return $result;
    }
    
    public function filterEntries(Container $entries, array $groups) : Container {
        $result = new Container();
        foreach ($entries->toArray() as $entry) {
            $add = false;
            foreach ($groups as $group) {
                if($entry['username'] === $group['username']){
                    $add = true;
                }
            }
            if($add){
                $result->add($entry);
            }
        }
        return $result;
    }
    
    public function getEntries(string $start, string $end) : Container {
        $startDate = new DateTime($start . ' 00:00:00');
        $endDate = new DateTime($end . '23:59:59');
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
    
    public function getNewActivityDetails() : Container {
        $details = new Container();
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $activityGroups = $cfg->get('activity_group');
        $overtime = $cfg->get('overtime');
        $details->add($activityGroups, 'groups');
        $details->add($overtime, 'overtime');
        return $details;
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
        $activity = $this->activity->getById($data->get('id_activity'));
        if($activity->get('assign_system')){
            $data->add(1, 'id_user');
            $user = $this->user->getById(1);
            $username = $user->get('username');
        }
        else{
            $username = $data->get('username');
            $user = $this->user->getByUsername($username);
            $data->add($user->get('id'), 'id_user');
        }
        $parser = new Entry();
        $entry = $parser->parse($data);
        $this->checkBlocker($entry);
        $this->checkEntryDuration($entry);
        $this->checkEntryDates($entry, $username);
        $idSchedule = $this->schedule->save($entry);
        if($data->isValueSet('id_document')){
            $idDocument = (int) $data->get('id_document');
            $this->saveDocumentSchedule($idSchedule, $idDocument);
        }
        return $idSchedule;
    }
    
    private function saveDocumentSchedule(int $idSchedule, int $idDocument) : void {
        $documentSchedule = new Container();
        $documentSchedule->add($idSchedule, 'id_schedule');
        $documentSchedule->add($idDocument, 'id_document');
        $this->documentSchedule->save($documentSchedule);
    }
    
    public function saveEntryForCurrentUser(Container $data) : int {
        $activity = $this->activity->getById($data->get('id_activity'));
        if($activity->get('assign_system')){
            $data->add(1, 'id_user');
            $user = $this->user->getById(1);
            $username = $user->get('username');
        }
        else{
            $username = Session::getUsername();
            $user = $this->user->getByUsername($username);
            $data->add($user->get('id'), 'id_user');
        }
        $parser = new Entry();
        $entry = $parser->parse($data);
        $this->checkBlocker($entry);
        $this->checkEntryDuration($entry);
        $this->checkEntryDates($entry, $username);
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
        $date = new DateTime(date(('Y-m') . '-1'));
        $item->add($date->format('Y-m-d'), 'active_from');
        $date->modify('+100 years');
        $item->add($date->format('Y-m-d'), 'active_to');
        $item->add($groupId, 'id_location_group');
        return $this->location->save($item);
    }
    
    private function getTemporaryGroupId() : int {
        $groups = $this->locationGroup->getByName('tmp');
        if($groups->isEmpty()){
            $item = new Container();
            $item->add('tmp', 'name');
            $item->add(0, 'active');
            $date = new DateTime(date(('Y-m') . '-1'));
            $item->add($date->format('Y-m-d'), 'active_from');
            $date->modify('+100 years');
            $item->add($date->format('Y-m-d'), 'active_to');
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
        $entry = $this->schedule->getById($id);
        $this->checkBlocker($entry);
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
        $entry = $this->scheduleEntries->getById($id);
        $active = $entry->get('active');
        if($active){
            $this->schedule->disable($id);
        }
        else{
            $this->checkEntryDates($entry, $entry->get('username'));
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
    
    private function checkEntryDates(Container $data, string $username) {
        if((int) $data->get('id_user') !== 1){
            $start = $data->get('start');
            $end = $data->get('end');
            $languages = Languages::getInstance();
            if($this->outOfEmploymentPeriods($data, $username)){
                throw new ScheduleEntryException($languages->get('entry_user_not_employed'));
            }
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
    
    private function outOfEmploymentPeriods(Container $entry, string $username) : bool {
        if((int) $entry->get('id_user') === 1){
            return false;
        }
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $periods = $this->usersEmploymentPeriods->getActiveByUser($username);
        $isOut = true;
        foreach ($periods->toArray() as $item){
            if($isOut){
                $period = new Container($item);
                $periodStart = new DateTime($period->get('start') . ' 00:00:00');
                $periodEnd = new DateTime($period->get('end') . ' 23:59:59');
                if($start >= $periodStart && $start <= $periodEnd && $end >= $periodStart && $end <= $periodEnd){
                    $isOut = false;
                }
            }
        }
        return $isOut;
    }
    
    private function checkBlocker(Container $entry) {
        $blocker = new ScheduleBlocker();
        if($blocker->isBLocked($entry)){
            throw new SystemBlockedException();
        }
    }
    
    private function checkEntryDuration(Container $entry) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $duration = (int) $end->format('Uv') - (int) $start->format('Uv');
            $languages = Languages::getInstance();
        if($duration > 86400000){   //24 hours
            throw new ScheduleEntryException($languages->get('entry_duration_over_limit'));
        }
        elseif($duration <= 0){
            throw new ScheduleEntryException($languages->get('start_earlier_than_end'));
        }
    }
    
}

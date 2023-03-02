<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\EquipmentDAO as EquipmentDAO;
use Data\Access\Tables\EquipmentTypeDAO as EquipmentTypeDAO;
use Data\Access\Tables\InventoryLogDAO as InventoryLogDAO;
use Data\Access\Views\EquipmentDetailsView as EquipmentDetailsView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Data\Access\Views\InventoryLogDetailsView as InventoryLogDetailsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Custom\Parsers\Database\Equipment as Equipment;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Database\DataFilter\DataFilter as DataFilter;
use Services\Exceptions\UnconfirmedEquipmentException as UnconfirmedEquipmentException;

/**
 * Description of InventoryService
 *
 * @author Tanzar
 */
class InventoryService {
    private EquipmentDAO $equipment;
    private EquipmentTypeDAO $equipmentType;
    private InventoryLogDAO $inventoryLog;
    private EquipmentDetailsView $equipmentDetails;
    private UsersEmploymentPeriodsView $userEmploymentPeriods;
    private UsersWithoutPasswordsView $users;
    private InventoryLogDetailsView $inventoryLogDetails;
    
    public function __construct() {
        $this->equipment = new EquipmentDAO();
        $this->equipmentType = new EquipmentTypeDAO();
        $this->inventoryLog = new InventoryLogDAO();
        $this->equipmentDetails = new EquipmentDetailsView();
        $this->userEmploymentPeriods = new UsersEmploymentPeriodsView();
        $this->users = new UsersWithoutPasswordsView();
        $this->inventoryLogDetails = new InventoryLogDetailsView();
    }
    
    public function getActiveEquipment() : Container {
        return $this->equipmentDetails->getActive();
    }
    
    public function getCurrentUserEquipment() : Container {
        $username = Session::getUsername();
        return $this->equipmentDetails->getActiveByUsername($username);
    }
    
    public function getUserEquipment(string $username) : Container {
        return $this->equipmentDetails->getActiveByUsername($username);
    }
    
    public function getEquipmentByState(string $state) : Container {
        return $this->equipmentDetails->getByState($state);
    }
    
    public function getEquipmentByType(int $idType) : Container {
        return $this->equipmentDetails->getActiveByType($idType);
    }
    
    public function getEquipmentByInventoryNumber(string $number) : Container {
        return $this->equipmentDetails->getActiveByInventoryNumber($number);
    }
    
    public function getEquipmentByRemarks(string $text) : Container {
        return $this->equipmentDetails->getActiveByRemarks($text);
    }
    
    public function getAllEquipmentTypes() : Container {
        return $this->equipmentType->getAll();
    }
    
    public function getActiveEquipmentTypes() : Container {
        return $this->equipmentType->getActive();
    }
    
    public function getTodayLogs() : Container {
        $today = date('Y-m-d');
        return $this->inventoryLogDetails->getByDate($today);
    }
    
    public function getLogsByFilters(Container $filters) : Container {
        $datafilter = new DataFilter('inventory_log_details', $filters);
        return $this->inventoryLogDetails->getFiltered($datafilter);
    }
    
    public function getEquipmentStates() : Container {
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $states = $cfg->get('equipment_state');
        return new Container($states);
    }
    
    public function getNewEquipmentDetails() : Container {
        $details = new Container();
        $users = $this->users->getActive();
        $details->add($users->toArray(), 'users');
        $types = $this->equipmentType->getActive();
        $details->add($types->toArray(), 'types');
        return $details;
    }
    
    public function getCurrentUserUnconfirmed() : Container {
        $username = Session::getUsername();
        return $this->inventoryLogDetails->getUnconfirmedForUser($username);
    }
    
    public function saveEquipmentType(Container $type) : int {
        return $this->equipmentType->save($type);
    }
    
    public function saveNewEquipment(Container $data) : int {
        $data->add('list', 'state');
        $data->add(date('Y-m-d'), 'calibration');
        $parser = new Equipment();
        $parsed = $parser->parse($data);
        $username = Session::getUsername();
        $user = $this->users->getByUsername($username);
        $userSourceId = (int) $user->get('id');
        $userTargetId = (int) $parsed->get('id_user');
        $parsed->add($userSourceId, 'id_user', true);
        $id = $this->equipment->save($parsed);
        $this->inventoryLog->newEquipment($id, $userSourceId, $userTargetId);
        return $id;
    }
    
    public function saveNewBorrowedEquipment(Container $data) : int {
        $data->add('borrowed', 'state');
        $data->add(date('Y-m-d'), 'calibration');
        $parser = new Equipment();
        $parsed = $parser->parse($data);
        $username = Session::getUsername();
        $user = $this->users->getByUsername($username);
        $userSourceId = (int) $user->get('id');
        $userTargetId = (int) $parsed->get('id_user');
        $parsed->add($userSourceId, 'id_user', true);
        $id = $this->equipment->save($parsed);
        $this->inventoryLog->borrowed($id, $userSourceId, $userTargetId);
        return $id;
    }
    
    public function editEquipment(Container $data) : void {
        $parser = new Equipment();
        $parsed = $parser->parse($data);
        $this->equipment->save($parsed);
    }
    
    public function assignEquipment(Container $equipment, bool $admin = false) : void {
        $equipmentId = (int) $equipment->get('id');
        if($admin){
            $this->inventoryLog->confirmAssigns($equipmentId);
        }
        $logs = $this->inventoryLogDetails->getUnconfirmedForEquipment($equipmentId);
        if($logs->length() === 0){
            $targetUserId = $this->getUserId($equipment->get('username'));
            $sourceUserId = $this->getUserId(Session::getUsername());
            $id = $this->inventoryLog->assign($equipmentId, $sourceUserId, $targetUserId);
        }
        else{
            throw new UnconfirmedEquipmentException();
        }
    }
    
    public function confirmEquipmentAssign(Container $log) : void {
        $logId = (int) $log->get('id');
        $this->inventoryLog->confirm($logId);
        $logDetails = $this->inventoryLogDetails->getById($logId);
        $targetUserId = (int) $logDetails->get('id_target_user');
        $equipmentId = (int) $logDetails->get('id_equipment');
        $this->equipment->changeUser($equipmentId, $targetUserId);
    }
    
    public function cancelEquipmentAssign(Container $log) : void {
        $logId = (int) $log->get('id');
        $this->inventoryLog->confirm($logId);
        $equipmentId = (int) $log->get('id_equipment');
        $userSourceId = (int) $log->get('id_source_user');
        $userTargetId = (int) $log->get('id_target_user');
        $this->inventoryLog->cancelAssign($equipmentId, $userSourceId, $userTargetId);
    }
    
    public function sendToRepair(string $date, string $document, int $equipmentId) : void {
        if(!$this->isBorrowed($equipmentId)){
            $username = Session::getUsername();
            $userId = $this->getUserId($username);
            $this->equipment->setAsRepair($equipmentId);
            $this->inventoryLog->repair($date, $document, $equipmentId, $userId);
        }
    }
    
    public function returnFromRepair(int $equipmentId) : void {
        $this->equipment->setAsOnList($equipmentId);
    }
    
    public function sendToCalibration(string $date, string $document, int $equipmentId) : void {
        if(!$this->isBorrowed($equipmentId)){
            $username = Session::getUsername();
            $userId = $this->getUserId($username);
            $this->equipment->setAsCalibration($equipmentId, $date);
            $this->inventoryLog->calibration($date, $document, $equipmentId, $userId);
        }
    }
    
    public function returnFromCalibration(int $equipmentId) : void {
        $this->equipment->setAsOnList($equipmentId);
    }
    
    public function liquidate(string $date, string $document, int $equipmentId) : void {
        $username = Session::getUsername();
        $userId = $this->getUserId($username);
        $equipment = $this->equipment->getById($equipmentId);
        if($equipment->get('state') === 'borrowed'){
            $this->equipment->setAsReturned($equipmentId);
            $this->inventoryLog->returned($date, $equipmentId, $userId);
        }
        elseif($equipment->get('state') === 'list'){
            $this->equipment->setAsLiquidation($equipmentId);
            $this->inventoryLog->liquidation($date, $document, $equipmentId, $userId);
        }
    }
    
    public function returnFromLiquidation(int $equipmentId) : void {
        $this->equipment->enable($equipmentId);
        $this->equipment->setAsOnList($equipmentId);
    }
    
    public function changeEquipmentTypeStatus(int $id) : void {
        $type = $this->equipmentType->getById($id);
        $active = $type->get('active');
        if($active){
            $this->equipmentType->disable($id);
        }
        else{
            $this->equipmentType->enable($id);
        }
    }
    
    private function getUserId(string $username) : int {
        $user = $this->users->getByUsername($username);
        return (int) $user->get('id');
    }
    
    private function isBorrowed(int $equipmentId) : bool {
        $equipment = $this->equipment->getById($equipmentId);
        return $equipment->get('state') === 'borrowed';
    }
}

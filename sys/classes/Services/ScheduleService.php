<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\ScheduleDataAccess as ScheduleDataAccess;
use Data\Access\UserDataAccess as UserDataAccess;
use Tanweb\Container as Container;
use Tanweb\Session as Session;

/**
 * Description of ScheduleService
 *
 * @author Tanzar
 */
class ScheduleService {
    private ScheduleDataAccess $scheduleData;
    private UserDataAccess $userData;
    
    public function __construct() {
        $this->scheduleData = new ScheduleDataAccess();
        $this->userService = new UserService();
    }
    
    public function getEntries(string $start, string $end) : Container{
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        return $this->scheduleData->getActiveEntries($startDate, $endDate);
    }
    
    public function getUserEntries(string $username, string $start, string $end) : Container {
        $user = $this->userData->getUserByUsername($username);
        $userId = $user->getValue('id');
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        return $this->scheduleData->getActiveUserEntries($userId, $startDate, $endDate);
    }
    
    public function getCurrentUserEntries(string $start, string $end) : Container {
        $username = Session::getUsername();
        $user = $this->userData->getUserByUsername($username);
        $userId = $user->getValue('id');
        $startDate = new DateTime($start);
        $endDate = new DateTime($end);
        return $this->scheduleData->getActiveUserEntries($userId, $startDate, $endDate);
    }
    
    public function addEntryForUser(Container $data) : int {
        $username = $data->getValue('username');
        $user = $this->userData->getUserByUsername($username);
        $userId = $user->getValue('id');
        $data->add($userId, 'id_user');
        return $this->scheduleData->create($data);
    }
    
    public function addEntryForCurrentUser(Container $data) : int {
        $username = Session::getUsername();
        $user = $this->userData->getUserByUsername($username);
        $userId = $user->getValue('id');
        $data->add($userId, 'id_user');
        return $this->scheduleData->create($data);
    }
}

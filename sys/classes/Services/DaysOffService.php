<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Data\Access\Tables\DaysOffUserDAO as DaysOffUserDAO;
use Data\Access\Views\DaysOffUserDetailsView as DaysOffUserDetailsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;

/**
 * Description of DaysOffService
 *
 * @author Tanzar
 */
class DaysOffService {
    private DaysOffDAO $daysOff;
    private DaysOffUserDAO $daysOffUser;
    private DaysOffUserDetailsView $daysOffUserDetails;
    private UsersWithoutPasswordsView $users;
    
    public function __construct() {
        $this->daysOff = new DaysOffDAO();
        $this->daysOffUser = new DaysOffUserDAO();
        $this->daysOffUserDetails = new DaysOffUserDetailsView();
        $this->users = new UsersWithoutPasswordsView();
    }
    
    public function getALL() : Container {
        return $this->daysOff->getAll();
    }
    
    public function getActiveForAll(int $month, int $year) : Container {
        return $this->daysOff->getActiveForAllByMonthAndYear($month, $year);
    }
    
    public function getUsersByDay(int $dayOffId) : Container {
        return $this->daysOffUserDetails->getByDayOff($dayOffId);
    }
    
    public function getCurrentUserDaysOff() : Container {
        $username = Session::getUsername();
        return $this->daysOffUserDetails->getByUsername($username);
    }
    
    public function getUserDays(string $username) : Container {
        return $this->daysOffUserDetails->getByUsername($username);
    }
    
    public function saveDayOff(Container $data) : int {
        return $this->daysOff->save($data);
    }
    
    public function saveDayForUser(string $username, int $dayOffId) : void {
        $user = $this->users->getByUsername($username);
        $userId = $user->get('id');
        $dayOffUser = $this->daysOffUser->getByIds($userId, $dayOffId);
        if($dayOffUser->length() === 0){    
            $item = new Container();
            $item->add($userId, 'id_user');
            $item->add($dayOffId, 'id_days_off');
            $this->daysOffUser->save($item);
        }
    }
    
    public function removeDayForUser(string $username, int $dayOffId) : void {
        $user = $this->users->getByUsername($username);
        $userId = $user->get('id');
        $dayOffUser = $this->daysOffUser->getByIds($userId, $dayOffId);
        $id = (int) $dayOffUser->get('id');
        $this->daysOffUser->remove($id);
    }
    
    public function changeDayOffStatus(int $id) : void {
        $dayOff = $this->daysOff->getById($id);
        $active = $dayOff->get('active');
        if($active){
            $this->daysOff->disable($id);
        }
        else{
            $this->daysOff->enable($id);
        }
    }
}

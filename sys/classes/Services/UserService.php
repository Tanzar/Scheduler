<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Tanweb\Container as Container;
use Tanweb\Security\Encrypter as Encrypter;
use Data\Access\Tables\UserDAO as UserDAO;
use Data\Access\Tables\PrivilageDAO as PrivilageDAO;
use Data\Access\Tables\EmploymentDAO as EmploymentDAO;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Data\Access\Views\UsersPrivilagesView as UsersPrivilagesView;
use Services\Exceptions\OverlapingPeriodsException as OverlapingPeriodsException;
use Services\Exceptions\LastAdminException as LastAdminException;

/**
 * Description of UserService
 *
 * @author Tanzar
 */
class UserService{
    private UserDAO $users;
    private PrivilageDAO $privilages;
    private EmploymentDAO $employment;
    private UsersEmploymentPeriodsView $usersEmploymentPeriods;
    private UsersWithoutPasswordsView $usersWithoutPasswords;
    private UsersPrivilagesView $usersPrivilages;
    
    public function __construct() {
        $this->users = new UserDAO();
        $this->privilages = new PrivilageDAO();
        $this->employment = new EmploymentDAO();
        $this->usersEmploymentPeriods = new UsersEmploymentPeriodsView();
        $this->usersWithoutPasswords = new UsersWithoutPasswordsView();
        $this->usersPrivilages = new UsersPrivilagesView();
    }
    
    public function getAllUsers() : Container {
        return $this->usersWithoutPasswords->getAll();
    }
    
    public function getActiveInspectors() : Container {
        return $this->usersPrivilages->getActiveInspectors();
    }
    
    public function getEmployedUsersListOrdered(string $date) : Container {
        return $this->usersEmploymentPeriods->getOrderedActiveByDate($date);
    }
    
    public function getUserByUsername(string $username) : Container {
        return $this->users->getByUsername($username);
    }
    
    public function getUserPrivilages(int $id) : Container {
        return $this->privilages->getByUserID($id);
    }
    
    public function getUserEmploymentPeriods(int $idUser) : Container {
        return $this->employment->getByUserId($idUser);
    } 
    
    public function getUserCurrentEmploymentPeriod(string $username) : Container {
        $today = date("Y-m-d");
        $periods = $this->usersEmploymentPeriods->getByUserAndDate($username, $today);
        if($periods->length() >= 1){
            return new Container($periods->get(0));
        }
        else{
            return new Container();
        }
    }
    
    public function findUsers(Container $conditions) : Container{
        return $this->usersWithoutPasswords->findUsers($conditions);
    }
    
    public function savePrivilage(Container $privilage) : int {
        $idUser = $privilage->get('id_user');
        $privilageName = $privilage->get('privilage');
        $privilages = $this->privilages->getByUserID($idUser);
        foreach ($privilages->toArray() as $data){
            $item = new Container($data);
            if($item->get('privilage') === $privilageName){
                $id = $item->get('id');
                $this->privilages->enable($id);
                return $id;
            }
        }
        return $this->privilages->save($privilage);
    }
    
    public function saveUser(Container $data) : int{
        if($data->isValueSet('password')){
            $password = $data->get('password');
            $encoded = Encrypter::encode($password);
            $data->add($encoded, 'password', true);
        }
        $id = $this->users->save($data);
        return $id;
    }
    
    public function saveEmploymentPeriod(Container $data) : int {
        $idUser = $data->get('id_user');
        $periods = $this->employment->getByUserId($idUser);
        foreach ($periods->toArray() as $item){
            $period = new Container($item);
            if($this->periodsAreOverlaping($data, $period)){
                throw new OverlapingPeriodsException();
            }
        }
        return $this->employment->save($data);
    }
    
    private function periodsAreOverlaping(Container $first, Container $second) : bool {
        if($first->isValueSet('id') && $second->isValueSet('id')){
            $firstId = (int) $first->get('id');
            $secondId = (int) $second->get('id');
            if($firstId === $secondId){
                return false;
            }
        }
        $firstStart = strtotime($first->get('start'));
        $firstEnd = strtotime($first->get('end'));
        $secondStart = strtotime($first->get('start'));
        $secondEnd = strtotime($first->get('end'));
        if(($firstStart >= $secondStart && $firstStart <= $secondEnd) ||
                ($firstEnd >= $secondStart && $firstEnd <= $secondStart)){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function changePassword(Container $data){
        $username = $data->get('username');
        $password = $data->get('password');
        $this->users->changePassword($username, $password);
    }
    
    public function changeUserStatus(Container $data){
        $user = $this->getUser($data);
        $active = $user->get('active');
        if($active){
            if($this->usersPrivilages->isLastAdmin()){
                throw new LastAdminException();
            }
            $this->users->disable($user->get('id'));
            
        }
        else{
            $this->users->enable($user->get('id'));
        }
    }
    
    public function changePrivilageStatus(int $id){
        $privilage = $this->privilages->getByID($id);
        $active = $privilage->get('active');
        if($active){
            if($privilage->get('privilage') === 'admin' && $this->usersPrivilages->isLastAdmin()){
                throw new LastAdminException();
            }
            $this->privilages->disable($privilage->get('id'));
            
        }
        else{
            $this->privilages->enable($privilage->get('id'));
        }
    }
    
    public function changeEmploymentStatus(int $id){
        $employmentPeriod = $this->employment->getById($id);
        $active = $employmentPeriod->get('active');
        if($active){
            $this->employment->disable($id);
        }
        else{
            $this->employment->enable($id);
        }
    }
    
    private function getUser(Container $data) : Container {
        if($data->isValueSet('id')){
            $id = $data->get('id');
            $user = $this->users->getById($id);
            
        }
        elseif($data->isValueSet('username')) {
            $username = $data->get('username');
            $user = $this->users->getByUsername($username);
        }
        return $user;
    }
}

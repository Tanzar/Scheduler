<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\SuzugUserDAO as SuzugUserDAO;
use Data\Access\Views\SuzugUserDetailsView as SuzugUserDetailsView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Tanweb\Container as Container;
use Custom\Parsers\Database\SuzugUser as SuzugUser;
use Services\Exceptions\SuzugNumberAssignedException as SuzugNumberAssignedException;
use Services\Exceptions\SuzugUserAssignedException as SuzugUserAssignedException;

/**
 * Description of SuzugService
 *
 * @author Tanzar
 */
class SuzugService {
    private SuzugUserDAO $suzugUser;
    private SuzugUserDetailsView $suzugUserDetails;
    private UsersEmploymentPeriodsView $usersEmploymentPeriods;

    public function __construct() {
        $this->suzugUser = new SuzugUserDAO();
        $this->suzugUserDetails = new SuzugUserDetailsView();
        $this->usersEmploymentPeriods = new UsersEmploymentPeriodsView();
    }
    
    public function getAssignmentOptions(int $year) : Container {
        $result = new Container();
        $assigned = $this->suzugUserDetails->getActiveByYear($year);
        $numbers = $this->getAvailableNumbers($assigned);
        $result->add($numbers->toArray(), 'numbers');
        $users = $this->getEmployedUsers($year, $assigned);
        $result->add($users->toArray(), 'users');
        return $result;
    }
    
    private function getAvailableNumbers(Container $assigned) : Container {
        $numbers = new Container();
        $number = 1;
        while($numbers->length() < 5 && $number <= 100){
            if($this->numberIsNotAssigned($assigned, $number)){
                $numbers->add($number);
            }
            $number++;
        }
        return $numbers;
    }
    
    private function numberIsNotAssigned(Container $assigned, int $number) : bool {
        foreach ($assigned->toArray() as $item) {
            $suzugUser = new Container($item);
            if(intval($suzugUser->get('number')) === $number){
                return false;
            }
        }
        return true;
    }
    
    private function getEmployedUsers(int $year, Container $assigned) : Container {
        $results = new Container();
        $usernames = new Container();
        foreach ($assigned->toArray() as $item) {
            $assign = new Container($item);
            $usernames->add($assign->get('username'));
        }
        $periods = $this->usersEmploymentPeriods->getOrderedInspectorsByYear($year);
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            if(!$usernames->contains($period->get('username'))){
                $usernames->add($period->get('username'));
                $results->add($period->get('name') . ' ' . $period->get('surname'), $period->get('id_user'));
            }
        }
        return $results;
    }
    
    public function getSuzugUsersForYear(int $year) : Container {
        $this->suzugUserDetails->getActiveByYear($year);
    }
    
    public function getAllSuzugUsersForYear(int $year) : Container {
        return $this->suzugUserDetails->getByYear($year);
    }
    
    public function save(Container $data) : int {
        $parser = new SuzugUser();
        $parsed = $parser->parse($data);
        $number = $parsed->get('number');
        $year = (int) $parsed->get('year');
        if($this->suzugUserDetails->isNotReserved($year, $number)){
            if($this->suzugUserDetails->isNotAssigned($year, (int) $parsed->get('id_user')) || $parsed->isValueSet('id')){
                return $this->suzugUser->save($parsed);
            }
            else{
                throw new SuzugUserAssignedException();
            }
        }
        else{
            throw new SuzugNumberAssignedException();
        }
    }
    
    public function changeSuzugUserStatus(int $id) : void {
        $item = $this->suzugUserDetails->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suzugUser->disable($id);
        }
        else{
            $year = (int) $item->get('year');
            $number = $item->get('number');
            $idUser = $item->get('id_user');
            if($this->suzugUserDetails->isNotReserved($year, $number)){
                if($this->suzugUserDetails->isNotAssigned($year, $idUser)){
                    $this->suzugUser->enable($id);
                }
                else{
                    throw new SuzugUserAssignedException();
                }
            }
            else{
                throw new SuzugNumberAssignedException();
            }
        }
        
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\LocationDetailsView as LocationDetailsView;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;

/**
 *
 * @author Tanzar
 */
enum Input : string {
    case Month = 'Miesiąc';
    case MonthsRange = 'Zakres miesięcy';
    case Year = 'Rok';
    case YearsRange = 'Zakres lat';
    case User = 'Użytkownik';
    case Inspector = 'Inspektor';
    case Location = 'Miejsce';
    case LocationGroup = 'Grupa miejsc';
    
    public function toJson() : Container {
        $result = new Container();
        $result->add($this->getVariableName(), 'variable');
        $result->add($this->value, 'title');
        $result->add($this->getValues(), 'values');
        return $result;
    }
    
    public function getVariableName() : string {
        return match($this){
            Input::Month => 'month',
            Input::MonthsRange => 'monthsRange',
            Input::Year => 'year',
            Input::YearsRange => 'yearsRange',
            Input::User => 'user',
            Input::Inspector => 'user',
            Input::Location => 'location',
            Input::LocationGroup => 'locationGroup'
        };
    }
    
    private function getValues() : array {
        return match($this){
            Input::Month => $this->getMonths(),
            Input::MonthsRange => $this->getMonths(),
            Input::Year => $this->getYears(),
            Input::YearsRange => $this->getYears(),
            Input::User => $this->getUsers(),
            Input::Inspector => $this->getInspectors(),
            Input::Location => $this->getLocations(),
            Input::LocationGroup => $this->getLocationGroups()
        };
    }
    
    public static function getColumn(Input $input) : string {
        return match($input){
            Input::Month => 'month(date)',
            Input::MonthsRange => 'month(date)',
            Input::Year => 'year(date)',
            Input::YearsRange => 'year(date)',
            Input::User => 'username',
            Input::Inspector => 'username',
            Input::Location => 'id_location',
            Input::LocationGroup => 'id_location_group'
        };
    }
    
    public static function getColumnByVariableName(string $variable) : string {
        return match($variable){
            'month' => Input::getColumn(Input::Month),
            'monthStart' => Input::getColumn(Input::MonthsRange),
            'monthEnd' => Input::getColumn(Input::MonthsRange),
            'year' => Input::getColumn(Input::Year),
            'yearStart' => Input::getColumn(Input::YearsRange),
            'yearEnd' => Input::getColumn(Input::YearsRange),
            'user' => Input::getColumn(Input::User),
            'location' => Input::getColumn(Input::Location),
            'locationGroup' => Input::getColumn(Input::LocationGroup),
            default => ''
        };
    }
    
    private function getMonths() : array {
        $language = Languages::getInstance();
        $months = $language->get('months');
        $result = array();
        foreach ($months as $number => $month){
            $result[] = array(
                'title' => $month,
                'value' => $number
            );
        }
        return $result;
    }
    
    private function getYears() : array {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $startYear = $cfg->get('yearStart');
        $result = array();
        for($year = $startYear; $year <= ((int) date('Y') + 1); $year++){
            $result[] = array(
                'title' => $year,
                'value' => $year
            );
        }
        return $result;
    }
    
    private function getUsers() : array {
        $view = new UsersEmploymentPeriodsView();
        $users = $view->getActive();
        $result = array();
        $usernames = new Container();
        foreach ($users->toArray() as $item) {
            $user = new Container($item);
            $username = $user->get('username');
            if(!$usernames->contains($username)){
                $result[] = array(
                    'value' => $user->get('username'),
                    'title' => $user->get('name') . ' ' . $user->get('surname')
                );
                $usernames->add($username);
            }
        }
        return $result;
    }
    
    private function getInspectors() : array {
        $view = new UsersEmploymentPeriodsView();
        $users = $view->getActiveInspectors();
        $result = array();
        $usernames = new Container();
        foreach ($users->toArray() as $item) {
            $user = new Container($item);
            $username = $user->get('username');
            if(!$usernames->contains($username)){
                $result[] = array(
                    'value' => $user->get('username'),
                    'title' => $user->get('name') . ' ' . $user->get('surname')
                );
                $usernames->add($username);
            }
        }
        return $result;
    }
    
    private function getLocations() : array {
        $view = new LocationDetailsView();
        $locations = $view->getInspectable();
        $result = array();
        foreach ($locations->toArray() as $item) {
            $location = new Container($item);
            $result[] = array(
                'value' => $location->get('id'),
                'title' => $location->get('name')
            );
        }
        return $result;
    }
    
    private function getLocationGroups() : array {
        $dao = new LocationGroupDAO();
        $groups = $dao->getInspectable();
        $result = array();
        foreach ($groups->toArray() as $item) {
            $group = new Container($item);
            $result[] = array(
                'value' => $group->get('id'),
                'title' => $group->get('name')
            );
        }
        return $result;
    }
    
    public function getTitleByValue($value) : string {
        $values = $this->getValues();
        foreach ($values as $item) {
            $option = new Container($item);
            if($option->get('value') === $value){
                return $option->get('title');
            }
        }
        return '';
    }
}

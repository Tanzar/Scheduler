<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Dates;

use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Data\Access\Tables\SpecialWorkdaysDAO as SpecialWorkdaysDAO;
use Data\Access\Views\DaysOffUserDetailsView as DaysOffUserDetailsView;
use Custom\Dates\HolidayChecker as HolidayChecker;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DayOffChecker
 *
 * @author Tanzar
 */
class DayOffChecker {
    private Container $daysOffAllUsers;
    private Container $daysOffByUsername;
    private Container $specialWorkdays;
    
    
    public function __construct() {
        $this->initDaysOffForAll();
        $this->initDaysOffByUsername();
        $this->initSpecialWorkdays();
    }
    
    private function initDaysOffForAll() : void {
        $this->daysOffAllUsers = new Container();
        $dao = new DaysOffDAO();
        $days = $dao->getActiveForAll();
        foreach ($days->toArray() as $item) {
            $this->daysOffAllUsers->add($item['date']);
        }
    }
    
    private function initDaysOffByUsername() : void {
        $this->daysOffByUsername = new Container();
        $view = new DaysOffUserDetailsView();
        $usersDays = $view->getAll();
        foreach ($usersDays->toArray() as $item) {
            $username = $item['username'];
            if($this->daysOffByUsername->isValueSet($username)){
                $dates = $this->daysOffByUsername->get($username);
                $dates[] = $item['days_off_date'];
                $this->daysOffByUsername->add($dates, $username, true);
            }
            else{
                $this->daysOffByUsername->add(array($item['days_off_date']), $username);
            }
        }
    }
    
    private function initSpecialWorkdays() : void {
        $dao = new SpecialWorkdaysDAO();
        $this->specialWorkdays = new Container();
        $workdays = $dao->getActive();
        foreach ($workdays->toArray() as $item) {
            $this->specialWorkdays->add($item['date']);
        }
    }
    
    public function isDayOff(DateTime $date, string $username = '') : bool {
        return ($this->isSaturday($date) ||
                $this->isSunday($date) ||
                $this->isHoliday($date) ||
                $this->isAssignedDayOff($date, $username))
                && $this->isNotSpecialWorkday($date);
    }
    
    public function isSaturday(DateTime $date) : bool {
        return (int) $date->format('N') === 6;
    }
    
    public function isSunday(DateTime $date) : bool {
        return (int) $date->format('N') === 7;
    }
    
    public function isHoliday(DateTime $date) : bool {
        return HolidayChecker::isHoliday($date);
    }
    
    public function isAssignedDayOff(DateTime $date, string $username = '') : bool {
        $dateString = $date->format('Y-m-d');
        $check = $this->daysOffAllUsers->contains($dateString);
        if(!$check && $username !== '' && $this->daysOffByUsername->isValueSet($username)){
            $array = $this->daysOffByUsername->get($username);
            $dates = new Container($array);
            $check = $dates->contains($dateString);
        }
        return $check;
    }
    
    public function isNotSpecialWorkday(DateTime $date) : bool {
        return !$this->specialWorkdays->contains($date->format('Y-m-d'));
    }
    
    public function isSpecialWorkday(DateTime $date) : bool {
        return $this->specialWorkdays->contains($date->format('Y-m-d'));
    }
}

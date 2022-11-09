<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools;

use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Data\Access\Views\DaysOffUserDetailsView as DaysOffUserDetailsView;
use Custom\Dates\HolidayChecker as HolidayChecker;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DaysOffTable
 *
 * @author Tanzar
 */
class DaysOffTable {
    private string $username;
    private int $month;
    private int $year;
    private Container $days;
    
    public function __construct(string $username, int $month, int $year) {
        $this->username = $username;
        $this->month = $month;
        $this->year = $year;
        $this->days = new Container();
        $this->loadDaysOffForAll();
        $this->loadDaysOffForUser();
        $this->loadWeekendsAndHolidays();
    }
    
    private function loadDaysOffForAll() : void {
        $dao = new DaysOffDAO();
        $daysForAll = $dao->getActiveForAllByMonthAndYear($this->month, $this->year);
        foreach ($daysForAll->toArray() as $item) {
            $day = new Container($item);
            $date = $day->get('date');
            $this->days->add($date);
        }
    }
    
    private function loadDaysOffForUser() : void {
        $view = new DaysOffUserDetailsView();
        $daysForUser = $view->getByUsernameMonthYear($this->username, $this->month, $this->year);
        foreach ($daysForUser->toArray() as $item) {
            $day = new Container($item);
            $date = $day->get('days_off_date');
            $this->days->add($date);
        }
    }
    
    private function loadWeekendsAndHolidays() : void {
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            $weekday = (int) $date->format('N');
            if($weekday === 6 || $weekday === 7 || HolidayChecker::isHoliday($date)){
                $this->days->add($date->format('Y-m-d'));
            }
        }
    }
    
    public function includes(DateTime $date) : bool {
        $dateStr = $date->format('Y-m-d');
        return $this->days->contains($dateStr);
    }
}

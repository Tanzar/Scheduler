<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Calculation\Times;

use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\SystemErrors\EntryException as EntryException;
use DateTime;

/**
 * Description of TimesheetsCalculator
 *
 * @author Tanzar
 */
class TimesheetsCalculator {
    private UsersEmploymentPeriodsView $usersEmploymentPeriodsView;
    private ScheduleEntriesView $scheduleEntriesView;
    private Container $appConfig;
    private string $username;
    private int $month;
    private int $year;
    private Container $periods;
    private Container $entries;
    private Container $timesPerDay;
    
    public function __construct(string $username, int $month, int $year) {
        $this->usersEmploymentPeriodsView = new UsersEmploymentPeriodsView();
        $this->scheduleEntriesView = new ScheduleEntriesView();
        $this->username = $username;
        $this->month = $month;
        $this->year = $year;
        $appconfig = AppConfig::getInstance();
        $this->appConfig = $appconfig->getAppConfig();
        $this->init();
    }
    
    private function init() : void {
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $date = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $this->periods = $this->usersEmploymentPeriodsView->getByUsernameToDate($this->username, $date);
        $this->entries = $this->scheduleEntriesView->getActiveByUsernameToDate($this->username, $date);
        $this->calculateTimes();
    }
    
    private function calculateTimes() : void {
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay; $day++){
            if($day < 9){
                $date = $this->timesPerDay->get($this->year . '-' . $this->month . '-0' . $day);
            }
            else{
                $date = $this->timesPerDay->get($this->year . '-' . $this->month . '-' . $day);
            }
            $results = $this->calculateForDay($date);
            $this->timesPerDay->add($results->toArray(), $date->format('Y-m-d'));
        }
    }
    
    private function calculateForDay(DateTime $date) : Container {
        $result = new Container();
        for($i = 0; $i < 15; $i++){
            $result->add(0, $i);
        }
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $period = $this->selectEmploymentPeriod($entry);
            $partialResult = $this->calculateForEntry($date, $period, $entry);
            $this->addToResult($result, $partialResult, $entry);
        }
        return $result;
    }
    
    private function selectEmploymentPeriod(Container $entry) : Container {
        $startDT = new DateTime($entry->get('start'));
        $start = $startDT->format('Y-m-d');
        foreach ($this->periods->toArray() as $item) {
            $period = new Container($item);
            if($start >= $period->get('start') && $start <= $period->get('end')){
                return $period;
            }
        }
        throw new EntryException($this->username);
    }
    
    private function calculateForEntry(DateTime $date, Container $period, Container $entry) : Container {
        $partialResult = new Container();
        $partialResult->add(0, 'overtime');
        $partialResult->add(0, 'worktime');
        $partialResult->add(0, 'nightShift');
        $entryStart = new DateTime($entry->get('start'));
        $entryEnd = new DateTime($entry->get('end'));
        if($entryStart->format('Y-m-d') === $date->format('Y-m-d') || $entryEnd->format('Y-m-d') === $date->format('Y-m-d')){
            $orderer = new Orderer();
            $this->addDayBreaks($orderer, $date);
            $this->addStandardDayTimes($orderer, $date, $period);
            $this->addEntryTimes($orderer, $entry);
            $this->addNightShiftTimes($orderer, $date);
            $partialResult = $orderer->countTimes();
        }
        return $partialResult;
    }
    
    private function addDayBreaks(Orderer $orderer, DateTime $date) : void {
        $dayBreakTime = $this->appConfig->get('workday_end_hour');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $dayBreakTime);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $dayBreakTime);
        $end->modify('+1 days');
        $orderer->addDayBreak($start, $end);
    }
    
    private function addStandardDayTimes(Orderer $orderer, DateTime $date, Container $period) : void {
        $dayStartTime = $period->get('standard_day_start');
        $dayEndTime = $period->get('standard_day_end');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $dayStartTime);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $dayEndTime);
        $orderer->addDayBreak($start, $end);
    }
    
    private function addEntryTimes(Orderer $orderer, Container $entry) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $orderer->addDayBreak($start, $end);
    }
    
    private function addNightShiftTimes(Orderer $orderer, DateTime $date) : void {
        $startHour = $this->appConfig->get('night_shift_start');
        $endHour = $this->appConfig->get('night_shift_end');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $startHour);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $endHour);
        if($endHour <= $startHour){
            $end->modify('+1 days');
        }
        $orderer->addDayBreak($start, $end);
    }
    
    private function addToResult(Container $result, Container $partialResult, Container $entry) : void {
        $overtimeRow = $this->appConfig->get('timesheets_overtime_row_index');
        $nightShiftRow = $this->appConfig->get('timesheets_night_shift_row_index');
        $row = $entry->get('worktime_record_row');
        if($row === 0 || $row === 1){
            $this->addValue($result, $partialResult, $row, 'worktime');
        }
        if($entry->get('overtime_action') === 'generates') {
            $this->addValue($result, $partialResult, $overtimeRow, 'overtime');
            $this->addValue($result, $partialResult, $nightShiftRow, 'nightShift');
        }
        if($entry->get('overtime_action') === 'reduces') {
            $this->addValue($result, $partialResult, 3, 'worktime');
        }
        $leaveRows = new Container(array(4, 5, 6, 7, 8, 9, 10, 11));
        if($leaveRows->contains($row)){
            $this->addValue($result, $partialResult, $row, 'worktime');
            $this->addValue($result, $partialResult, $row, 'overtime');
        }
    }
    
    private function addValue(Container $result, Container $partialResult, int $row, string $key) : void {
        $value = $result->get($row);
        $value += $partialResult->get($key);
        $result->add($value, $row, true);
    }
    
    public function getTimesForDay(int $day) : Container{
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        if($day > 0 && $day <= $lastDay){
            if($day < 9){
                $item = $this->timesPerDay->get($this->year . '-' . $this->month . '-0' . $day);
            }
            else{
                $item = $this->timesPerDay->get($this->year . '-' . $this->month . '-' . $day);
            }
            return new Container($item);
        }
    }
}

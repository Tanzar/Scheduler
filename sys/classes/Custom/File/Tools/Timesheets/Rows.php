<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Custom\File\Tools\DaysOffTable as DaysOffTable;
use Custom\File\Tools\Timesheets\UniqueWorkHours as UniqueWorkHours;
use Custom\File\Tools\Timesheets\TimesCalculator as TimesCalculator;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\OvertimeReductionDetailsView as OvertimeReductionDetailsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use DateTime;

/**
 * Description of Rows
 *
 * @author Tanzar
 */
class Rows {
    //main vars
    private Container $rows;
    
    //supportive vars
    private int $month;
    private int $year;
    private string $username;
    private Container $periods;
    private Container $entries;
    private Container $currentMonthTimes;
    private Container $previousMonthsTimes;
    private DaysOffTable $daysOff;
    private UniqueWorkHours $uniqueWorkHours;
    
    public function __construct(int $month, int $year, string $username) {
        $this->month = $month;
        $this->year = $year;
        $this->username = $username;
        $this->init();
    }
    
    private function init() : void {
        $this->calculateTimes();
        $this->loadDaysOff();
        $this->initUniqueWorkHours();
        $this->prepareRowsData();
    }
    
    private function calculateTimes() : void {
        $periods = $this->getEmploymentPeriods();
        $this->periods = $periods;
        $previousMonthsEntries = $this->getPreviousMonthsEntries();
        $this->entries = $this->getMonthEntries();
        $this->calculatePreviousMonths($periods, $previousMonthsEntries);
        $this->calculateCurrentMonthTimes($periods);
    }
    
    private function getEmploymentPeriods() : Container {
        $view = new UsersEmploymentPeriodsView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $date = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        return $view->getByUsernameToDate($this->username, $date);
    }
    
    private function getPreviousMonthsEntries() : Container {
        $view = new ScheduleEntriesView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $date = new DateTime($this->year . '-' . $this->month . '-01' . ' 23:59:59');
        $date->modify('-1 days');
        return $view->getActiveByUsernameToDateOredrDescOvertimeAction($this->username, $date);
    }
    
    private function getMonthEntries() : Container {
        $view = new ScheduleEntriesView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $start = new DateTime($this->year . '-' . $this->month . '-01' . ' 00:00:00');
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        return $view->getActiveByUsernameAndDatesOredrDescOvertimeAction($this->username, $start, $end);
    }
    
    private function calculatePreviousMonths(Container $periods, Container $previousMonthsEntries) : void {
        $earliestDate = new DateTime();
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            $start = new DateTime($period->get('start') . ' 00:00:00');
            if($start < $earliestDate){
                $earliestDate = $start;
            }
        }
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $this->previousMonthsTimes = TimesCalculator::calculate($periods, $previousMonthsEntries, $earliestDate, $end);
    }
    
    private function calculateCurrentMonthTimes(Container $periods) : void {
        $earliestDate = new DateTime($this->year . '-' . $this->month . '-01' . ' 00:00:00');
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $this->currentMonthTimes = TimesCalculator::calculate($periods, $this->entries, $earliestDate, $end);
    }
    
    private function loadDaysOff() {
        $this->daysOff = new DaysOffTable($this->username, $this->month, $this->year);
    }
    
    private function initUniqueWorkHours() : void {
        $view = new UsersEmploymentPeriodsView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $start = new DateTime($this->year . '-' . $this->month . '-01 00:00:00');
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $periods = $view->getByUsernameAndDatesRange($this->username, $start, $end);
        $this->uniqueWorkHours = new UniqueWorkHours($periods, $this->entries);
    }
    
    private function prepareRowsData() : void {
        $this->rows = new Container();
        $this->rows->add($this->initDaysNumbersRow(), 'daysNumbers');
        $this->rows->add($this->initWeekdaysRow(), 'weekDays');
        $this->rows->add($this->initDaysTypeLetterRow(), 'dayTypes');
        $this->rows->add($this->initDaysRomanLetterRow(), 'uniqueWorkHours');
        $this->rows->add($this->initDayStandardWorkHoursRow(), 'standardWorkHours');
        $symbolRows = new Container(array(5, 6, 11));
        for($row = 0; $row < 15; $row++){
            $this->rows->add($this->initRowHours($row), $row);
            if($symbolRows->contains($row)){
                $this->rows->add($this->initSymbolRow($row), 's' . $row);
            }
        }
    }
    
    private function initDaysNumbersRow() : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $result[$day] = $day;
        }
        return $result;
    }
    
    private function initWeekdaysRow() : array {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $weekdays = new Container($cfg->get('weekdays_short'));
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            $weekday = (int) $date->format('N');
            $result[$day] = $weekdays->get($weekday);
        }
        return $result;
    }
    
    private function initDaysTypeLetterRow() : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            if($this->daysOff->includes($date)){
                $result[$day] = 'W';
            }
            else{
                $result[$day] = 'P';
            }
        }
        return $result;
    }
    
    private function initDaysRomanLetterRow() : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            if($this->haveDayAnyTimes($date)){
                $result[$day] = $this->uniqueWorkHours->getHighestRoman($this->periods, $date);
            }
        }
        return $result;
    }
    
    private function haveDayAnyTimes(DateTime $date) : bool {
        $dayTimes = $this->currentMonthTimes->get($date->format('Y-m-d'));
        $found = false;
        foreach ($dayTimes as $value) {
            if($value > 0){
                $found = true;
            }
        }
        return $found;
    }
    
    private function initDayStandardWorkHoursRow() : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day . ' 12:00:00');
            $time = $this->getPeriodTime($date);
            $result[$day] = $time;
        }
        return $result;
    }
    
    private function getPeriodTime(DateTime $date) : int {
        $time = 0;
        foreach ($this->periods->toArray() as $item){
            $period = new Container($item);
            $start = new DateTime($period->get('start') . ' 00:00:00');
            $end = new DateTime($period->get('end') . ' 23:59:59');
            $dayStart = new DateTime($period->get('start') . ' ' . $period->get('standard_day_start'));
            $dayEnd = new DateTime($period->get('start') . ' ' . $period->get('standard_day_end'));
            if($date >= $start && $date <= $end && !$this->isDayOff($date)){
                $time = (int) $dayEnd->format('Uv') - (int) $dayStart->format('Uv');
            }
        }
        return $time;
    }
    
    private function initRowHours(int $row) : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day . ' 12:00:00');
            $time = $this->getRowTimes($row, $date);
            $result[$day] = $time;
        }
        return $result;
    }
    
    private function getRowTimes(int $row, DateTime $date) : int {
        $formated = $date->format('Y-m-d');
        $values = $this->currentMonthTimes->get($formated);
        if(isset($values[$row])){
            return $values[$row];
        }
        else{
            return 0;
        }
    }
    
    public function initSymbolRow(int $row) : array {
        $result = array();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $result[$day] = $this->getSymbol($row, $day);
        }
        return $result;
    }
    
    public function getSymbol(int $row, int $day) : string {
        $symbol = '';
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            $entryStart = new DateTime($entry->get('start'));
            $entryEnd = new DateTime($entry->get('end'));
            $entryRow = (int) $entry->get('worktime_record_row');
            if(($date->format('Y-m-d') === $entryStart->format('Y-m-d') 
                    || $date->format('Y-m-d') === $entryEnd->format('Y-m-d')) 
                    && $row === $entryRow){
                $symbol = $entry->get('activity_symbol');
            }
        }
        return $symbol;
    }
    
    public function getRow(string $row) : array {
        return $this->rows->get($row);
    }
    
    public function isDayOff(DateTime $date) : bool {
        return $this->daysOff->includes($date);
    }
    
    public function getUniqueHoursSets() : Container {
        return new Container($this->uniqueWorkHours->toArray());
    }
    
    public function countWorkDays() : int {
        $count = 0;
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay ; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            if($day <= $lastDay && !$this->daysOff->includes($date) && $this->isEmployed($date)){
                $count++;
            }
        }
        return $count;
    }
    
    private function isEmployed(DateTime $date) : bool {
        $found = false;
        foreach ($this->periods->toArray() as $item) {
            $period = new Container($item);
            $start = new DateTime($period->get('start'));
            $end = new DateTime($period->get('end'));
            if($date >= $start && $date <= $end){
                $found = true;
            }
        }
        return $found;
    }
    
    public function summarizeRow(string $row) : int {
        $rowData = $this->rows->get($row);
        $sum = 0;
        foreach ($rowData as $time) {
            $sum += intval($time);
        }
        return $sum;
    }
    
    public function calculatePreviousOvertime() : int {
        $count = 0;
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $overtimeRow = $cfg->get('timesheets_overtime_row_index');
        foreach ($this->previousMonthsTimes->toArray() as $item) {
            $count += (int) $item[$overtimeRow];
            $count = $count - (int) $item[3];
        }
        return $count - $this->getPreviousOvertimeReduction();
    }
    
    private function getPreviousOvertimeReduction() : int {
        $view = new OvertimeReductionDetailsView();
        $date = new DateTime($this->year . '-' . $this->month . '-1');
        $date->modify('-1 months');
        $reductions = $view->getActiveByUsernameBeforeOrAt($this->username, $date);
        $count = 0;
        foreach ($reductions->toArray() as $item) {
            $reduction = new Container($item);
            $count += (int) $reduction->get('time');
        }
        return $count;
    }
    
    public function calculateCurrentWorktime() : int {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $nightShiftRow = $cfg->get('timesheets_night_shift_row_index');
        $ignoreRows = new Container(array($nightShiftRow, 3));
        $count = 0;
        foreach ($this->currentMonthTimes->toArray() as $item) {
            for($row = 0 ; $row < count($item); $row++){
                if(!$ignoreRows->contains($row)){
                    $count += (int) $item[$row];
                }
            }
        }
        return $count;
    }
    
    public function calculatePassingOvertime() : int {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $orvetimeRow = $cfg->get('timesheets_overtime_row_index');
        $overtimeReductionRow = $cfg->get('timesheets_overtime_reduction_row_index');
        $currentOvertime = $this->summarizeRow($orvetimeRow);
        $previousOvertime = $this->calculatePreviousOvertime();
        $currentOvertimeReduction = $this->getCurrentOvertimeReduction() + $this->summarizeRow($overtimeReductionRow);
        return $currentOvertime + $previousOvertime - $currentOvertimeReduction;
    }
    
    private function getCurrentOvertimeReduction() : int {
        $view = new OvertimeReductionDetailsView();
        $date = new DateTime($this->year . '-' . $this->month . '-1');
        $reductions = $view->getActiveByUsernameAt($this->username, $date);
        $count = 0;
        foreach ($reductions->toArray() as $item) {
            $reduction = new Container($item);
            $count += (int) $reduction->get('time');
        }
        return $count;
    }
}

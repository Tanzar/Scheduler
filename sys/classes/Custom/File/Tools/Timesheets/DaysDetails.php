<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Custom\Dates\DayOffChecker as DayOffChecker;
use Custom\File\Tools\Timesheets\DayDetails as DayDetails;
use Custom\File\exceptions\EntryOutOfEmploymentException as EntryOutOfEmploymentException;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use DateTime;

/**
 * Description of DaysDetails
 *
 * @author Tanzar
 */
class DaysDetails {
    private Container $details;
    
    private Container $periods;
    private Container $entries;
    private string $username;
    private DayOffChecker $checker;
    private string $nightShiftStartTime;
    private string $nightShiftEndTime;
    private Container $cfg;
    
    public function __construct(string $username, Container $periods, Container $entries) {
        $this->details = new Container();
        $this->username = $username;
        $this->periods = $periods;
        $this->entries = $entries;
        $this->checker = new DayOffChecker();
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $this->nightShiftStartTime = $cfg->get('night_shift_start');
        $this->nightShiftEndTime = $cfg->get('night_shift_end');
        $this->cfg = $cfg;
        $this->init();
    }
    
    private function init() : void {
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $this->analyzeEntry($entry);
        }
        $this->fillEmptyDates();
    }
    
    private function analyzeEntry(Container $entry) : void {
        if((int) $entry->get('id_user') !== 1){
            $period = $this->getMatchingPeriod($entry);
            $dayBreakHour = $period->get('standard_day_start');
            $this->calculateEntry($entry, $period, $dayBreakHour);
        }
    }
    
    private function getMatchingPeriod(Container $entry) : Container {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        foreach ($this->periods->toArray() as $item) {
            $period = new Container($item);
            $periodStart = new DateTime($period->get('start'));
            $periodEnd = new DateTime($period->get('end') . ' 23:59:59');
            if(($start >= $periodStart && $start <= $periodEnd) ||
                ($end >= $periodStart && $end <= $periodEnd)){
                return $period;
            }
        }
        throw new EntryOutOfEmploymentException($this->username . ', start = ' . $entry->get('start'));
    }
    
    private function calculateEntry(Container $entry, Container $period, string $dayBreakHour) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $dayBreak = new DateTime($start->format('Y-m-d') . ' ' . $dayBreakHour);
        if($dayBreak > $start){
            $dayBreak->modify('-1 days');
        }
        while($dayBreak < $end){
            $this->calculateEntryForDate($entry, $dayBreak, $period);
            $dayBreak->modify('+1 days');
        }
    }
    
    private function calculateEntryForDate(Container $entry, DateTime $dayStart, Container $period) : void {
        if($this->details->isValueSet($dayStart->format('Y-m-d'))){
            $details = $this->getDetails($dayStart);
        }
        else{
            $details = $this->addEmptyDetails($period, $dayStart);
        }
        $worktime = $this->calculateWorktime($entry, $dayStart);
        $nightShift = $this->calculateNightShift($entry, $dayStart);
        $details->assignEntryTimes($entry, $worktime, $nightShift);
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $details->setWorkdayStart($start->format('H:i'));
        $details->setWorkdayEnd($end->format('H:i'));
    }
    
    private function calculateWorktime(Container $entry, DateTime $dayStart) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $countEnd = new DateTime($dayStart->format('Y-m-d H:i:s'));
        $countEnd->modify('+1 days');
        $worktimeStart = max($start, $dayStart);
        $worktimeEnd = max($start, $dayStart, min($end, $countEnd));
        return (int) $worktimeEnd->format('Uv') - (int) $worktimeStart->format('Uv');
    }
    
    private function calculateNightShift(Container $entry, DateTime $dayStart) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $countStart = new DateTime($dayStart->format('Y-m-d') . ' ' . $this->nightShiftStartTime);
        $countEnd = new DateTime($dayStart->format('Y-m-d') . ' ' . $this->nightShiftEndTime);
        if($countEnd < $countStart){
            $countEnd->modify('+1 days');
        }
        $timeStart = max($start, $countStart);
        $timeEnd = max($start, $countStart, min($end, $countEnd));
        return (int) $timeEnd->format('Uv') - (int) $timeStart->format('Uv');
    }
    
    private function fillEmptyDates() : void {
        foreach ($this->periods->toArray() as $item){
            $period = new Container($item);
            $this->fillEmptyDatesForPeriod($period);
        }
    }
    
    private function fillEmptyDatesForPeriod(Container $period) : void {
        $date = new DateTime($period->get('start'));
        $periodEnd = new DateTime($period->get('end'));
        $limit = new DateTime();
        $limit->modify('+2 months');
        $end = min($limit, $periodEnd);
        while($date <= $end){
            $dateString = $date->format('Y-m-d');
            if(!$this->details->isValueSet($dateString)){
                $this->addEmptyDetails($period, $date);
            }
            $date->modify('+1 days');
        }
    }
    
    private function addEmptyDetails(Container $period, DateTime $date) : DayDetails {
        if($this->checker->isDayOff($date, $this->username)){
            $details = new DayDetails(0);
        }
        else{
            $dayStart = new DateTime($date->format('Y-m-d') . ' ' . $period->get('standard_day_start'));
            $workdayEnd = new DateTime($dayStart->format('Y-m-d') . ' ' . $period->get('standard_day_end'));
            $standardWorkdayTime = (int) $workdayEnd->format('Uv') - (int) $dayStart->format('Uv');
            $details = new DayDetails($standardWorkdayTime);
        }
        $this->setDateDetails($details, $date);
        $this->details->add($details, $date->format('Y-m-d'));
        return $details;
    }
    
    private function setDateDetails(DayDetails $details, DateTime $date) : void {
        $weekdays = $this->cfg->get('weekdays_short');
        $short = $weekdays[(int) $date->format('N')];
        $details->setWeekdayText($short . '.');
        if($this->checker->isSpecialWorkday($date)){
            $details->setDayLetter ('P');
        }
        elseif($this->checker->isHoliday($date)){
            $details->setDayLetter ('Ś');
        }
        elseif($this->checker->isAssignedDayOff($date, $this->username) || $this->checker->isSaturday($date)){
            $details->setDayLetter('W');
        }
        elseif($this->checker->isSunday($date)){
            $details->setDayLetter('N');
        }
        else{
            $details->setDayLetter('P');
        }
    }
    
    public function sumOvertime(DateTime $date) : int {
        $sum = 0;
        foreach ($this->details->toArray() as $key => $item) {
            if($date > new DateTime($key)){
                $sum += $item->getOvertime();
            }
        }
        return $sum;
    }
    
    public function sumWZN(DateTime $date) : int {
        $sum = 0;
        foreach ($this->details->toArray() as $key => $item) {
            if($date > new DateTime($key)){
                $sum += $item->getWznTime();
            }
        }
        return $sum;
    }
    
    public function sumWorkdays(int $year, int $month) : int {
        $date = new DateTime($year . '-' . $month . '-01');
        $end = new DateTime($year . '-' . $month . '-' . $date->format('t'));
        $sum = 0;
        while($date <= $end){
            $details = $this->getDetails($date);
            $letter = $details->getDayLetter();
            if($letter === 'P'){
                $sum++;
            }
            $date->modify('+1 days');
        }
        return $sum;
    }
    
    public function getDetails(DateTime $date) : DayDetails {
        $key = $date->format('Y-m-d');
        if($this->details->isValueSet($key)){
            return $this->details->get($key);
        }
        else{
            return new DayDetails(0);
        }
    }
    
    public function toArray() : array {
        return $this->details->toArray();
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DayDetails
 *
 * @author Tanzar
 */
class DayDetails {
    private string $dayLetter = 'W';
    private int $standardWorkdayTime = 0;
    private string $workdayStart = '';
    private string $workdayEnd = '';
    private int $officeTime = 0;
    private int $delegationTime = 0;
    private int $overtime = 0;
    private int $wznTime = 0;
    private int $nightShiftTime = 0;
    private string $absenceSymbol = '';
    private int $absenceTime = 0;
    private string $workOffSymbol = '';
    private int $workOffTime = 0;
    private string $weekdayText = '';
    
    public  function __construct(int $standardWorkdayTime) {
        $this->standardWorkdayTime = $standardWorkdayTime;
    }
    
    public function setDayLetter(string $letter) : void {
        $this->dayLetter = $letter;
    }
    
    public function setWorkdayStart(string $time) : void {
        $this->workdayStart = $time;
    }
    
    public function setWorkdayEnd(string $time) : void {
        $this->workdayEnd = $time;
    }
    
    public function addWorkTime(int $office, int $delegation, bool $generateOvertime = false) : void {
        $officeTimeTotal = $this->officeTime + $office;
        $delegationTimeTotal = $this->delegationTime + $delegation;
        if($officeTimeTotal + $delegationTimeTotal > $this->standardWorkdayTime){
            $this->calculateTimes($office, $delegation, $generateOvertime);
        }
        else{
            $this->officeTime += $office;
            $this->delegationTime += $delegation;
        }
    }
    
    private function calculateTimes(int $office, int $delegation, bool $generateOvertime = false) : void {
        $officeTime = $this->officeTime + $office;
        $total = $officeTime + $this->delegationTime + $delegation;
        $overtime = max($total - $this->standardWorkdayTime, 0);
        $this->officeTime = min($officeTime, $this->standardWorkdayTime);
        $this->delegationTime = max($total - $overtime - $this->officeTime, 0);
        if($generateOvertime){
            $this->overtime = $overtime;
        }
    }
    
    public function addOvertime(int $time) : void {
        $this->overtime += $time;
    }
    
    public function addWZN(int $time) : void {
        $this->wznTime += $time;
    }
    
    public function addNightShiftTime(int $time) : void {
        $this->nightShiftTime += $time;
    }
    
    public function setAbsenceSymbol(string $symbol) : void {
        $this->absenceSymbol = $symbol;
    }
    
    public function addAbsenceTime(int $time) : void {
        $this->absenceTime += $time;
    }
    
    public function setWorkOffSymbol(string $symbol) : void {
        $this->workOffSymbol = $symbol;
    }
    
    public function addWorkOffTime(int $time) : void {
        $this->workOffTime += $time;
    }
    
    public function getDayLetter(): string {
        return $this->dayLetter;
    }

    public function getStandardWorkdayTime(): int {
        return $this->standardWorkdayTime;
    }
    
    public function getWorkdayStart(): string {
        return $this->workdayStart;
    }

    public function getWorkdayEnd(): string {
        return $this->workdayEnd;
    }

    public function getOfficeTime(): int {
        return $this->officeTime;
    }

    public function getDelegationTime(): int {
        return $this->delegationTime;
    }

    public function getOvertime(): int {
        return $this->overtime;
    }

    public function getWznTime(): int {
        return $this->wznTime;
    }

    public function getNightShiftTime(): int {
        return $this->nightShiftTime;
    }

    public function getAbsenceSymbol(): string {
        return $this->absenceSymbol;
    }

    public function getAbsenceTime(): int {
        return $this->absenceTime;
    }

    public function getWorkOffSymbol(): string {
        return $this->workOffSymbol;
    }

    public function getWorkOffTime(): int {
        return $this->workOffTime;
    }
    
    public function assignEntryTimes(Container $entry, int $worktime, int $nightShift) : void {
        $row = (int) $entry->get('worktime_record_row');
        $overtimeAction = $entry->get('overtime_action');
        $generateOvertime = $overtimeAction === 'generates';
        if($row === 22){
            $this->addWorkTime($worktime, 0, $generateOvertime);
        }
        elseif($row === 23){
            $this->addWorkTime(0, $worktime, $generateOvertime);
        }
        elseif($row === 25){
            $this->addWZN($worktime);
        }
        elseif($row === 28){
            $this->addAbsenceTime($worktime);
            $this->setAbsenceSymbol($entry->get('activity_symbol'));
        }
        elseif($row === 30){
            $this->addWorkOffTime($worktime);
            $this->setWorkOffSymbol($entry->get('activity_symbol'));
        }
        $this->addNightShiftTime($nightShift);
    }
    
    public function getWeekdayText(): string {
        return $this->weekdayText;
    }

    public function setWeekdayText(string $weekdayText): void {
        $this->weekdayText = $weekdayText;
    }

    public function sumTimes() : int {
        return $this->officeTime + $this->delegationTime + $this->overtime
                + $this->wznTime + $this->absenceTime + $this->workOffTime;
    }
}

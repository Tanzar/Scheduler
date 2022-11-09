<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\SystemErrors\EntryException as EntryException;
use DateTime;

/**
 * Description of TimesCalculator
 *
 * @author Tanzar
 */
class TimesCalculator {
    
    public static function calculate(Container $periods, Container $entries, DateTime $start, DateTime $end) : Container {
        $result = new Container();
        if($start < $end){
            $date = new DateTime($start->format('Y-m-d'));
            while($date->format('Y-m-d') <= $end->format('Y-m-d')){
                $dateTimes = self::calculateForDate($periods, $entries, $date);
                $result->add($dateTimes->toArray(), $date->format('Y-m-d'));
                $date->modify('+1 day');
            }
        }
        return $result;
    }
    
    private static function calculateForDate(Container $periods, Container $entries, DateTime $date) : Container {
        $period = self::selectEmploymentPeriod($periods, $date);
        if($period->isEmpty()){
            $result = new Container();
            for($i = 0; $i < 15; $i++){
                $result->add(0, $i);
            }
            return $result;
        }
        else{
            return self::calculateForEmployed($periods, $entries, $date);
        }
    }
    
    private static function calculateForEmployed(Container $periods, Container $entries, DateTime $date) {
        $period = self::selectEmploymentPeriod($periods, $date);
        $dateStart = new DateTime($date->format('Y-m-d') . ' ' . $period->get('standard_day_start'));
        $dateEnd = new DateTime($date->format('Y-m-d') . ' ' . $period->get('standard_day_start'));
        $dateEnd->modify('+1 days');
        $result = new Container();
        for($i = 0; $i < 15; $i++){
            $result->add(0, $i);
        }
        foreach ($entries->toArray() as $item) {
            $entry = new Container($item);
            $entryStart = new DateTime($entry->get('start'));
            $entryEnd = new DateTime($entry->get('end'));
            if(($entryStart >= $dateStart && $entryStart <= $dateEnd) ||
                    ($entryEnd >= $dateStart && $entryEnd <= $dateEnd)){
                $partialResult = self::calculateEntry($periods, $entry, $date);
                self::addPartialResult($result, $partialResult, $entry);
            }
        }
        return $result;
    }
    
    private static function calculateEntry(Container $periods, Container $entry, DateTime $date) : Container {
        $period = self::selectEmploymentPeriod($periods, $date);
        $orderer = new Orderer();
        self::addDayBreaks($orderer, $date, $period);
        self::addStandardDayTimes($orderer, $date, $period);
        self::addEntryTimes($orderer, $entry);
        self::addNightShiftTimes($orderer, $date);
        $partialResult = $orderer->countTimes();
        return $partialResult;
    }
    
    private static function selectEmploymentPeriod(Container $periods, DateTime $date) : Container {
        $start = $date->format('Y-m-d');
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            if($start >= $period->get('start') && $start <= $period->get('end')){
                return $period;
            }
        }
        return new Container();
    }
    
    private static function addDayBreaks(Orderer $orderer, DateTime $date, Container $period) : void {
        $dayBreakTime = $period->get('standard_day_start');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $dayBreakTime);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $dayBreakTime);
        $end->modify('+1 days');
        $orderer->addDayBreak($start, $end);
    }
    
    private static function addStandardDayTimes(Orderer $orderer, DateTime $date, Container $period) : void {
        $dayStartTime = $period->get('standard_day_start');
        $dayEndTime = $period->get('standard_day_end');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $dayStartTime);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $dayEndTime);
        $orderer->addStandard($start, $end);
    }
    
    private static function addEntryTimes(Orderer $orderer, Container $entry) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $orderer->addEntry($start, $end);
    }
    
    private static function addNightShiftTimes(Orderer $orderer, DateTime $date) : void {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $startHour = $cfg->get('night_shift_start');
        $endHour = $cfg->get('night_shift_end');
        $start = new DateTime($date->format('Y-m-d') . ' ' . $startHour);
        $end = new DateTime($date->format('Y-m-d') . ' ' . $endHour);
        if($endHour <= $startHour){
            $end->modify('+1 days');
        }
        $orderer->addNightShift($start, $end);
    }
    
    private static function addPartialResult(Container $result, Container $partialResult, Container $entry) : void {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $overtimeRow = $cfg->get('timesheets_overtime_row_index');
        $overtimeReductionRow = $cfg->get('timesheets_overtime_reduction_row_index');
        $nightShiftRow = $cfg->get('timesheets_night_shift_row_index');
        $row = $entry->get('worktime_record_row');
        if($row === 0 || $row === 1){
            self::addValue($result, $partialResult, $row, 'worktime');
        }
        if($entry->get('overtime_action') === 'generates') {
            self::addValue($result, $partialResult, $overtimeRow, 'overtime');
            self::addValue($result, $partialResult, $nightShiftRow, 'nightShift');
        }
        if($entry->get('overtime_action') === 'consumes') {
            self::addValue($result, $partialResult, $overtimeReductionRow, 'worktime');
        }
        $leaveRows = new Container(array(4, 5, 6, 7, 8, 9, 10, 11));
        if($leaveRows->contains($row)){
            self::addValue($result, $partialResult, $row, 'worktime');
        }
    }
    
    private static function addValue(Container $result, Container $partialResult, int $row, string $key) : void {
        $value = $result->get($row);
        $value += $partialResult->get($key);
        $result->add($value, $row, true);
    }
    
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Calculation;

use Custom\Statistics\Calculation\ResultSet as ResultSet;
use Custom\Statistics\Calculation\ResultItem as ResultItem;
use Tanweb\Container as Container;
use Custom\Statistics\Options\Group as Group;
use Custom\Statistics\Options\Shift as Shift;
use Tanweb\Config\INI\AppConfig as AppConfig;
use DateTime;

/**
 * Description of Calculator
 *
 * @author Tanzar
 */
class Calculator {
    
    public static function sum(Container $data, Container $groups, string $columnToSum) : ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $dataRow = new Container($item);
            $keys = array();
            foreach ($groups->toArray() as $group) {
                if($group === Group::Users){
                    $value = $group->getValue($dataRow);
                }
                else{
                    $value = (int) $group->getValue($dataRow);
                }
                $keys[$group->getColumn()] = $value;
            }
            $value = (float) $dataRow->get($columnToSum);
            $resultItem = new ResultItem($keys, $value);
            $result->add($resultItem);
        }
        return $result;
    }
    
    public static function count(Container $data, Container $groups) : ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $dataRow = new Container($item);
            $keys = array();
            foreach ($groups->toArray() as $group) {
                if($group === Group::Users || $group === Group::UserTypes){
                    $value = $group->getValue($dataRow);
                }
                else{
                    $value = (int) $group->getValue($dataRow);
                }
                $keys[$group->getColumn()] = $value;
            }
            $resultItem = new ResultItem($keys, 1);
            $result->add($resultItem);
        }
        return $result;
    }
    
    public static function countWorkdays(Container $data, Container $groups) : ResultSet {
        $countedWorkdays = new Container();
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $entry = new Container($item);
            self::countEntryAsWorkday($countedWorkdays, $entry, $result, $groups);
        }
        return $result;
    }
    
    private static function countEntryAsWorkday(Container $countedWorkdays, Container $entry, ResultSet $resultSet, Container $groups) : void {
        $date = self::getDate($entry);
        $standardWorktime = self::getStandardWorkTime($entry);
        $worktime = self::getWorkTime($entry);
        $count = ($worktime >= $standardWorktime) ? 1 : 0;
        if($count > 0){
            $key = $entry->get('username') . '-' . $date;
            if (!$countedWorkdays->contains($key)){
                $countedWorkdays->add($key);
                $resultItem = self::formCountEntryResultItem($entry, $groups, $date, $count);
                $resultSet->add($resultItem);
            }
        }
    }
    
    private static function getStandardWorkTime(Container $entry) : int {
        $start = new DateTime('2000-01-01 ' . $entry->get('standard_day_start'));
        $end = new DateTime('2000-01-01 ' . $entry->get('standard_day_end'));
        return (int) $end->format('Uv') - (int) $start->format('Uv');
    }
    
    private static function getWorkTime(Container $entry) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        return (int) $end->format('Uv') - (int) $start->format('Uv');
    }
    
    public static function countNightShifts(Container $data, Container $groups) : ResultSet {
        $countedDays = new Container();
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $entry = new Container($item);
            self::countEntryAsNightShift($countedDays, $entry, $result, $groups);
        }
        return $result;
    }
    
    private static function countEntryAsNightShift(Container $countedWorkdays, Container $entry, ResultSet $resultSet, Container $groups) : void {
        $date = self::getDate($entry);
        $count = self::getNightShiftCount($date, $entry);
        if($count > 0){
            $key = $entry->get('username') . '-' . $date;
            if (!$countedWorkdays->contains($key)){
                $countedWorkdays->add($key);
                $resultItem = self::formCountEntryResultItem($entry, $groups, $date, $count);
                $resultSet->add($resultItem);
            }
        }
    }
    
    private static function getNightShiftCount(string $date, Container $entry) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $nightShiftStart = new DateTime($date . ' ' . $cfg->get('night_shift_start'));
        $nightShiftEnd = new DateTime($date . ' ' . $cfg->get('night_shift_end'));
        if($nightShiftEnd < $nightShiftStart){
            $nightShiftEnd->modify('+1 days');
        }
        if($nightShiftStart >= $start && $nightShiftEnd <= $end){
            return 1;
        }
        return 0;
    }
    
    
    public static function countShift(Container $data, Container $groups, Shift $shift) : ResultSet {
        $countedDays = new Container();
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $entry = new Container($item);
            self::countEntryAsShift($countedDays, $entry, $result, $groups, $shift);
        }
        return $result;
    }
    
    private static function countEntryAsShift(Container $countedWorkdays, Container $entry, ResultSet $resultSet, Container $groups, Shift $shift) : void {
        $date = self::getDate($entry);
        $count = self::getShiftCount($date, $entry, $shift);
        if($count > 0){
            $key = $entry->get('username') . '-' . $date;
            if (!$countedWorkdays->contains($key)){
                $countedWorkdays->add($key);
                $resultItem = self::formCountEntryResultItem($entry, $groups, $date, $count);
                $resultSet->add($resultItem);
            }
        }
    }
    
    private static function getShiftCount(string $date, Container $entry, Shift $shift) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $shiftTimes = new Container($cfg->get($shift->value));
        $shiftStart = new DateTime($date . ' ' . $shiftTimes->get('start'));
        $shiftEnd = new DateTime($date . ' ' . $shiftTimes->get('end'));
        if($shiftEnd < $shiftStart){
            $shiftEnd->modify('+1 days');
        }
        if($shiftStart >= $start && $shiftEnd <= $end){
            return 1;
        }
        return 0;
    }
    
    
    private static function getDate(Container $entry) : string {
        $start = new DateTime($entry->get('start'));
        $dayBreak = new DateTime($start->format('Y-m-d') . ' ' . $entry->get('standard_day_start'));
        if($start < $dayBreak){
            $start->modify('-1 days');
            return $start->format('Y-m-d');
        }
        return $start->format('Y-m-d');
    }
    
    private static function formCountEntryResultItem(Container $entry, Container $groups, string $date, float $count) : ResultItem {
        $dateTime = new DateTime($date);
        $keys = array();
        foreach ($groups->toArray() as $group) {
            if($group === Group::Users){
                $value = $group->getValue($entry);
            }
            elseif($group !== Group::Days && $group !== Group::Months && $group !== Group::Years){
                $value = $group->getValue($entry);
            }
            elseif($group === Group::Days){
                $value = (int) $dateTime->format('j');
            }
            elseif($group === Group::Months){
                $value = (int) $dateTime->format('n');
            }
            elseif($group === Group::Years){
                $value = (int) $dateTime->format('Y');
            }
            $keys[$group->getColumn()] = $value;
        }
        return new ResultItem($keys, $count);
    }
    
}

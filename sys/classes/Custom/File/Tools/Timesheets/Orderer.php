<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Tanweb\Container as Container;
use Custom\Dates\HolidayChecker as HolidayChecker;
use DateTime;

/**
 * Description of Orderer
 *
 * @author Tanzar
 */
class Orderer {
    private array $items;
    private int $standardWorktime = 0;
    private int $weekday = 1;
    private DateTime $day;
    private string $overtimeAction = '';
    
    public function __construct() {
        $this->items = array();
    }
    
    public function addDayBreak(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'dayBreakStart');
        $this->addDate($end, 'dayBreakEnd');
        $this->weekday = (int) $start->format('N');
        $this->day = $start;
    }
    
    public function addEntry(Container $entry) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $this->overtimeAction = $entry->get('overtime_action');
        $this->addDate($start, 'entryStart');
        $this->addDate($end, 'entryEnd');
    }
    
    public function addStandard(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'standardStart');
        $this->addDate($end, 'standardEnd');
        $this->standardWorktime = (int) $end->format('Uv') - $start->format('Uv');
    }
    
    public function addNightShift(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'nightShiftStart');
        $this->addDate($end, 'nightShiftEnd');
    }
    
    public function addDate(DateTime $date, string $type) : void {
        $newItem = array(
            'date' => $date,
            'type' => $type
        );
        if(count($this->items) === 0){
            $this->items[] = $newItem;
        }
        else{
            $found = false;
            foreach ($this->items as $index => $item){
                if(!$found && $item['date'] > $newItem['date']){
                    $inserted = array($newItem);
                    array_splice( $this->items, $index, 0, $inserted );
                    $found = true;
                }
            }
            if(!$found){
                $this->items[] = $newItem;
            }
        }
    }
    
    public function toArray() : array {
        return $this->items;
    }
    
    public function countTimes(int $currentWorktime) : Container {
        $result = new Container();
        $result->add(0, 'overtime');
        $result->add($currentWorktime, 'worktime');
        $result->add(0, 'nightShift');
        $this->countAll($result);
        $worktime = $result->get('worktime');
        $newWorktime = $worktime - $currentWorktime;
        $result->add($newWorktime, 'worktime', true);
        return $result;
    }
    
    private function countAll(Container $result) : void {
        $inDay = false;
        $inEntry = false;
        $inNightShift = false;
        for ($a = 0 , $b = 1 ; $b < count($this->items) ; $a++, $b++){
            $first = $this->items[$a];
            $second = $this->items[$b];
            if($first['type'] === 'entryStart'){
                $inEntry = true;
            }
            if($first['type'] === 'entryEnd'){
                $inEntry = false;
            }
            if($first['type'] === 'dayBreakStart'){
                $inDay = true;
            }
            if($first['type'] === 'dayBreakEnd'){
                $inDay = false;
            }
            if($first['type'] === 'nightShiftStart'){
                $inNightShift = true;
            }
            if($first['type'] === 'nightShiftEnd'){
                $inNightShift = false;
            }
            if($inDay && $inEntry){
                $time = (int) $second['date']->format('Uv') - $first['date']->format('Uv');
                $worktime = $result->get('worktime');
                $result->add($worktime + $time, 'worktime', true);
            }
            if($inDay && $inEntry && $inNightShift){
                $time = (int) $second['date']->format('Uv') - $first['date']->format('Uv');
                $worktime = $result->get('nightShift');
                $result->add($worktime + $time, 'nightShift', true);
            }
        }
        $worktime = $result->get('worktime');
        if($this->overtimeAction === 'generates'){
            if($worktime > $this->standardWorktime){
                $result->add($this->standardWorktime, 'worktime', true);
                $result->add($worktime - $this->standardWorktime, 'overtime', true);
            }
            if($this->weekday === 6 || $this->weekday === 7 || HolidayChecker::isHoliday($this->day)){
                $result->add(0, 'worktime', true);
                $result->add($worktime, 'overtime', true);
            }
        }
    }
}

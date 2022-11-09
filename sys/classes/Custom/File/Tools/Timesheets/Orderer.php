<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Tanweb\Container as Container;
use DateTime;

/**
 * Description of Orderer
 *
 * @author Tanzar
 */
class Orderer {
    private array $items;
    
    public function __construct() {
        $this->items = array();
    }
    
    public function addDayBreak(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'dayBreakStart');
        $this->addDate($end, 'dayBreakEnd');
    }
    
    public function addEntry(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'entryStart');
        $this->addDate($end, 'entryEnd');
    }
    
    public function addStandard(DateTime $start, DateTime $end) : void {
        $this->addDate($start, 'standardStart');
        $this->addDate($end, 'standardEnd');
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
    
    public function countTimes() : Container {
        $result = new Container();
        $result->add(0, 'overtime');
        $result->add(0, 'worktime');
        $result->add(0, 'nightShift');
        $this->countAll($result);
        return $result;
    }
    
    private function countAll(Container $result) : void {
        $inEntry = false;
        $stage = -1;
        for ($a = 0 , $b = 1 ; $b < count($this->items) ; $a++, $b++){
            $first = $this->items[$a];
            $second = $this->items[$b];
            $time = (int) $second['date']->format('Uv') - $first['date']->format('Uv');
            if($first['type'] === 'entryStart'){
                $inEntry = true;
            }
            if($first['type'] === 'entryEnd'){
                $inEntry = false;
            }
            if($first['type'] === 'dayBreakStart' && !$inEntry){
                $stage++;
            }
            if($inEntry){
                $this->countToStage($result, $time, $stage);
            }
            if($this->moveStage($second)){
                $stage++;
            }
        }
    }
    
    private function countToStage(Container $result, int $time, int $stage) {
        switch ($stage){
            case 0:
                $this->addTime($result, $time, 'overtime');
                break;
            case 1:
                $this->addTime($result, $time, 'worktime');
                break;
            case 2:
                $this->addTime($result, $time, 'overtime');
                break;
            case 3:
                $this->addTime($result, $time, 'overtime');
                $this->addTime($result, $time, 'nightShift');
                break;
            case 4:
                $this->addTime($result, $time, 'overtime');
                break;
        }
    }
    
    private function addTime(Container $result, int $time, string $key) : void {
        $value = $result->get($key);
        $value += $time;
        $result->add($value, $key, true);
    }
    
    private function moveStage(array $second) : bool {
        return $second['type'] === 'dayBreakStart' || 
                $second['type'] === 'standardStart' || 
                $second['type'] === 'standardEnd' || 
                $second['type'] === 'nightShiftStart' || 
                $second['type'] === 'nightShiftEnd' || 
                $second['type'] === 'dayBreakEnd';
    }
    
}

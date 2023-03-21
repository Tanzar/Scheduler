<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations;

use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\CalculationStrategy as CalculationStrategy;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of CountShiftB
 *
 * @author Tanzar
 */
class CountShiftB implements CalculationStrategy {
    private Container $waitingList;
    private Container $processedData;
    private GroupsContainer $gorups;
    private string $shiftStart;
    private string $shiftEnd;
    private int $shiftTime;
    
    public function calculate(Container $data, GroupsContainer $groups): ResultSet {
        $this->gorups = $groups;
        $this->waitingList = new Container();
        $this->processedData = new Container();
        $this->setShiftTimes();
        foreach ($data->toArray() as $item) {
            $entry = new Container($item);
            $this->processEntry($entry);
        }
        return $this->count($this->processedData, $groups);
    }
    
    private function setShiftTimes() : void {
        $app = AppConfig::getInstance();
        $cfg = $app->getAppConfig();
        $shift = $cfg->get('shift_b');
        $this->shiftStart = $shift['start'];
        $this->shiftEnd = $shift['end'];
        $this->shiftTime = $this->calculateShiftInMinutes();
    }
    
    private function calculateShiftInMinutes() : int {
        $start = new DateTime('2000-01-01 ' . $this->shiftStart);
        $end = new DateTime('2000-01-01 ' . $this->shiftEnd);
        if($start > $end){
            $end->modify('+1 days');
        }
        $timeInMs = (int) $end->format('Uv') - (int) $start->format('Uv');
        return floor($timeInMs / (1000 * 60));
    }
    
    
    private function processEntry(Container $entry) : void {
        $date = new DateTime($entry->get('start'));
        $date->modify('-1 days');
        for($i = 1; $i <= 3; $i++){
            $time = $this->calculateShiftTimeInMinutes($date, $entry);
            if($time >= $this->shiftTime){
                $this->addProcessed($date, $entry->get('username'), $entry);
            }
            elseif($time > 0 && $time < $this->shiftTime){
                $this->assignWaiting($date, $entry, $time);
                    
            }
            $date->modify('+1 days');
        }
    }
    
    private function calculateShiftTimeInMinutes(DateTime $date, Container $entry) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $shiftStart = new DateTime($date->format('Y-m-d') . ' ' . $this->shiftStart);
        $shiftEnd = new DateTime($date->format('Y-m-d') . ' ' . $this->shiftEnd);
        $time = max(0, min((int) $end->format('Uv'), (int) $shiftEnd->format('Uv')) 
                - max((int) $start->format('Uv'), (int) $shiftStart->format('Uv')));
        return floor($time / (1000 * 60));
    }
    
    private function assignWaiting(DateTime $date, Container $entry, int $time) : void {
        $index = $date->format('Y-m-d') . ';' . $entry->get('username');
        if(!$this->processedData->isValueSet($index)){
            if($this->waitingList->isValueSet($index)){
                $this->checkWaitingList($date, $entry, $time);
            }
            else{
                $parsed = $this->parseEntry($date, $entry);
                $parsed['time'] = $time;
                $items = array($parsed);
                $this->waitingList->add($items, $index);
            }
        }
    }
    
    private function checkWaitingList(DateTime $date, Container $entry, int $time) : void {
        $index = $date->format('Y-m-d') . ';' . $entry->get('username');
        $items = $this->waitingList->get($index);
        $notFound = true;
        foreach ($items as $itemsIndex => $item) {
            $parsed = $this->parseEntry($date, $entry);
            if($this->areParsedEqual($item, $parsed)){
                $notFound = false;
                $this->manageWaitingItem($item, $time, $index, $itemsIndex, $entry->get('username'));
            }
        }
        if($notFound){
            $parsed['time'] = $time;
            $items[] = $parsed;
            $this->waitingList->add($items, $index, true);
        }
    }
    
    private function areParsedEqual(array $item, array $parsed) : bool {
        $equal = true;
        foreach ($item as $key => $value) {
            if($key !== 'time' && (!isset($parsed[$key]) || $parsed[$key] !== $value)){
                $equal = false;
            }
        }
        return $equal;
    }
    
    private function manageWaitingItem(array $item, int $time, string $index, int $itemsIndex, string $username) : void {
        $waitingTime = (int) $item['time'] + $time;
        if($waitingTime < $this->shiftTime){
            $item['time'] = $waitingTime;
            $items = $this->waitingList->get($index);
            $items[$itemsIndex] = $item;
            $this->waitingList->add($items, $index, true);
        }
        else{
            $date = new DateTime($item['date']);
            $entry = new Container($item);
            $this->addProcessed($date, $username, $entry);
            $items = $this->waitingList->get($index);
            $this->waitingList->remove($index);
            
        }
    }
    
    public function count(Container $data, GroupsContainer $groups): ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item) {
            $row = new Container($item);
            $grouping = $this->determineGrouping($row, $groups);
            $result->addValue($grouping, 1);
        }
        return $result;
    }
    
    private function determineGrouping(Container $row, GroupsContainer $groups) : GroupsContainer{
        $grouping = $groups->copy();
        foreach ($grouping->toArray() as $group) {
            $group->setValue($row);
        }
        return $grouping;
    }
    
    private function addProcessed(DateTime $date, string $username, Container $entry) : void {
        $resultItem = $this->parseEntry($date, $entry);
        $index = $date->format('Y-m-d') . ';' . $username;
        if(!$this->processedData->isValueSet($index)){
            $this->processedData->add($resultItem, $index);
        }
    }
    
    private function parseEntry(DateTime $date, Container $entry) : array {
        $resultItem = array(
            'date' => $date->format('Y-m-d')
        );
        $groups = $this->gorups->copy();
        foreach ($groups->toArray() as $group) {
            $variable = $group->getValueVariableName();
            if($variable !== 'date'){
                $resultItem[$variable] = $entry->get($variable);
            }
        }
        return $resultItem;
    }
}

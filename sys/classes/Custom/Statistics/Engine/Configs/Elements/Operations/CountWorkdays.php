<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations;

use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\CalculationStrategy as CalculationStrategy;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of CountWorkdays
 *
 * @author Tanzar
 */
class CountWorkdays implements CalculationStrategy {
    private Container $waitingList;
    private Container $processedData;
    private GroupsContainer $gorups;
    
    public function calculate(Container $data, GroupsContainer $groups): ResultSet {
        $this->gorups = $groups;
        $this->waitingList = new Container();
        $this->processedData = new Container();
        foreach ($data->toArray() as $item) {
            $entry = new Container($item);
            $this->processEntry($entry);
        }
        return $this->count($this->processedData, $groups);
    }
    
    private function processEntry(Container $entry) : void {
        $time = $this->calculateTimeInMinutes($entry);
        $worktime = $this->calculateWorktimeInMinutes($entry);
        if($time >= $worktime){
            $this->assignEntry($entry, $time, $worktime);
        }
        else{
            $this->assignWaiting($entry, $time, $worktime);
        }
    }
    
    private function calculateTimeInMinutes(Container $entry) : int {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $timeInMs = (int) $end->format('Uv') - (int) $start->format('Uv');
        return floor($timeInMs / (1000 * 60));
    }
    
    private function calculateWorktimeInMinutes(Container $entry) : int {
        $start = new DateTime('2000-01-01 ' . $entry->get('standard_day_start'));
        $end = new DateTime('2000-01-01 ' . $entry->get('standard_day_end'));
        if($start > $end){
            $end->modify('+1 days');
        }
        $timeInMs = (int) $end->format('Uv') - (int) $start->format('Uv');
        return floor($timeInMs / (1000 * 60));
    }
    
    private function assignEntry(Container $entry, int $time, int $worktime) : void {
        $dates = $this->determineDates($entry, $time, $worktime);
        foreach ($dates as $item) {
            $date = new DateTime($item);
            $this->addProcessed($date, $entry->get('username'), $entry);
        }
    }
    
    private function assignWaiting(Container $entry, int $time, int $worktime) : void {
        $dates = $this->determineDates($entry, $time, $worktime);
        $date = new DateTime($dates[0]);
        $parsed = $this->parseEntry($date, $entry);
        $index = $date->format('Y-m-d') . ';' . $entry->get('username');
        if(!$this->processedData->isValueSet($index)){
            if($this->waitingList->isValueSet($index)){
                $this->checkWaitingList($parsed, $time, $worktime, $index, $entry->get('username'));
            }
            else{
                $parsed['time'] = $time;
                $items = array($parsed);
                $this->waitingList->add($items, $index);
            }
        }
    }
    
    private function checkWaitingList(array $parsed, int $time, int $worktime, string $index, string $username) : void {
        $items = $this->waitingList->get($index);
        $notFound = true;
        foreach ($items as $itemsIndex => $item) {
            if($this->areParsedEqual($item, $parsed)){
                $notFound = false;
                $this->manageWaitingItem($item, $time, $worktime, $index, $itemsIndex, $username);
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
    
    private function manageWaitingItem(array $item, int $time, int $worktime, string $index, int $itemsIndex, string $username) : void {
        $waitingTime = (int) $item['time'] + $time;
        if($waitingTime < $worktime){
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
    
    private function determineDates(Container $entry, int $time, int $worktime) : array {
        $start = new DateTime($entry->get('start'));
        $daybreak = new DateTime($start->format('Y-m-d') . ' ' . 
                $entry->get('standard_day_start'));
        if($time >= (2 * $worktime)){
            return $this->determineDatesForDoubleWorktime($entry, $daybreak, $worktime);
        }
        else{
            if($start < $daybreak){
                $start->modify('-1 days');
            }
            return array($start->format('Y-m-d'));
        }
    }
    
    private function determineDatesForDoubleWorktime(Container $entry, DateTime $daybreak, int $worktime) : array {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        if($start >= $daybreak){
            $daybreak->modify('+1 days');
        }
        $firstTimeInMs = (int) $daybreak->format('Uv') - (int) $start->format('Uv');
        $firstDayTime = floor($firstTimeInMs / (1000 * 60));
        $secondTimeInMs = (int) $end->format('Uv') - (int) $daybreak->format('Uv');
        $secondDayTime = floor($secondTimeInMs / (1000 * 60));
        if($firstDayTime >= $worktime && $secondDayTime >= $worktime){
            $result = array($daybreak->format('Y-m-d'));
            $daybreak->modify('-1 days');
            $result[] = $daybreak->format('Y-m-d');
            return $result;
        }
        else{
            return array($start->format('Y-m-d'));
        }
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

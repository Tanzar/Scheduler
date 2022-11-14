<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Custom\Converters\Roman as Roman;
use Tanweb\Container as Container;
use Custom\SystemErrors\EntryException as EntryException;
use DateTime;
/**
 * Description of UniqueWorkHours
 *
 * @author Tanzar
 */
class UniqueWorkHours {
    private Container $entries;
    private Container $hoursSets;
    
    public function __construct(Container $periods, Container $entries) {
        $this->hoursSets = new Container();
        $this->entries = $entries;
        $this->initSets($periods);
        foreach ($entries->toArray() as $item) {
            $entry = new Container($item);
            $this->check($entry);
        }
    }
    
    private function initSets(Container $periods) : void {
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            $hours = new Container();
            $hours->add('2000-01-01 ' . $period->get('standard_day_start'), 'start');
            $hours->add('2000-01-01 ' . $period->get('standard_day_end'), 'end');
            $this->addSet($hours);
        }
    }
    
    private function check(Container $entry) : void {
        $found = false;
        foreach ($this->hoursSets->toArray() as $item) {
            $set = new Container($item);
            if($this->doHoursMatch($set, $entry)){
                $found = true;
            }
        }
        if(!$found){
            $this->addSet($entry);
        }
    }
    
    private function addSet(Container $entry) : void {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $startHour = $start->format('H:i:s');
        $endHour = $end->format('H:i:s');
        $number = Roman::toRoman($this->hoursSets->length() + 1);
        $item = array(
            'start' => $startHour,
            'end' => $endHour
        );
        $this->hoursSets->add($item, $number);
    }
    
    public function getHighestRoman(Container $periods, DateTime $date) : string {
        $result = '';
        $period = $this->selectEmploymentPeriod($periods, $date);
        $dateStart = new DateTime($date->format('Y-m-d') . ' ' . $period->get('standard_day_start'));
        $dateEnd = new DateTime($date->format('Y-m-d') . ' ' . $period->get('standard_day_start'));
        $dateEnd->modify('+1 day');
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $start = new DateTime($entry->get('start'));
            $end = new DateTime($entry->get('end'));
            $roman = '';
            if(($start >= $dateStart && $start <= $dateEnd) || ($end >= $dateStart && $end <= $dateEnd)){
                $roman = $this->getHigherRoman($entry, $result);
            }
            if($roman !== ''){
                $result = $roman;
            }
        }
        return $result;
    }
    
    private function selectEmploymentPeriod(Container $periods, DateTime $date) : Container {
        $start = $date->format('Y-m-d');
        foreach ($periods->toArray() as $item) {
            $period = new Container($item);
            if($start >= $period->get('start') && $start <= $period->get('end')){
                return $period;
            }
        }
        throw new EntryException();
    }
    
    public function getHigherRoman(Container $entry, string $current) : string {
        $roman = $this->getRoman($entry);
        $romanInt = Roman::toInt($roman);
        $currentInt = Roman::toInt($current);
        if($current !== ''){
            if($romanInt < $currentInt){
                return $current;
            }
            else{
                return $roman;
            }
        }
        else{
            return $roman;
        }
    }
    
    public function getRoman(Container $entry) : string {
        foreach ($this->hoursSets->toArray() as $number => $item) {
            $set = new Container($item);
            if($this->doHoursMatch($set, $entry)){
                return $number;
            }
        }
        return '';
    }
    
    private function doHoursMatch(Container $set, Container $entry) : bool {
        $start = new DateTime($entry->get('start'));
        $end = new DateTime($entry->get('end'));
        $startHour = $start->format('H:i:s');
        $endHour = $end->format('H:i:s');
        return $set->get('start') === $startHour && $set->get('end') === $endHour;
    }
    
    public function toArray() : array {
        return $this->hoursSets->toArray();
    }
}

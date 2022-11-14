<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\File\Tools\Workcard;

use Custom\File\Tools\DaysOffTable as DaysOffTable;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use DateTime;

/**
 * Description of Rows
 *
 * @author Tanzar
 */
class Rows {
    private Container $rows;
    
    private int $month;
    private int $year;
    private string $username;
    private Container $periods;
    private Container $entries;
    private DaysOffTable $daysOff;
    
    public function __construct(int $month, int $year, string $username) {
        $this->month = $month;
        $this->year = $year;
        $this->username = $username;
        $this->init();
    }
    
    private function init() : void {
        $this->periods = $this->getEmploymentPeriods();
        $this->daysOff = new DaysOffTable($this->username, $this->month, $this->year);
        $this->entries = $this->geEntries();
        $this->prepareRows();
    }
    
    private function getEmploymentPeriods() : Container {
        $view = new UsersEmploymentPeriodsView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $date = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        return $view->getByUsernameToDate($this->username, $date);
    }
    
    private function geEntries() : Container {
        $view = new ScheduleEntriesView();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $start = new DateTime($this->year . '-' . $this->month . '-01' . ' 00:00:00');
        $start->modify('-1 days');
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $end->modify('+1 days');
        return $view->getActiveByUsernameAndDates($this->username, $start, $end);
    }
    
    private function prepareRows() : void {
        $this->rows = new Container();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            $period = $this->selectEmploymentPeriod($date);
            $breakHour = $period->get('standard_day_start');
            $start = new DateTime($this->year . '-' . $this->month . '-' . $day . ' ' . $breakHour);
            $end = new DateTime($this->year . '-' . $this->month . '-' . $day . ' ' . $breakHour);
            $end->modify('+1 days');
            $this->makeRowsForDay($day, $start, $end);
        }
    }
    
    private function selectEmploymentPeriod(DateTime $date) : Container {
        $start = $date->format('Y-m-d');
        foreach ($this->periods->toArray() as $item) {
            $period = new Container($item);
            if($start >= $period->get('start') && $start <= $period->get('end')){
                return $period;
            }
        }
        return new Container();
    }
    
    private function makeRowsForDay(int $day, DateTime $start, DateTime $end) : void {
        $addedRows = 0;
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $entryStart = new DateTime($entry->get('start'));
            if($entryStart >= $start && $entryStart < $end){
                $this->addRow($day, $entry);
                $addedRows++;
            }
        }
        if($addedRows === 0){
            $this->addEmptyRow($day);
        }
    }
    
    private function addRow(int $day, Container $entry) : void {
        $entryStart = new DateTime($entry->get('start'));
        $entryEnd = new DateTime($entry->get('end'));
        $row = array(
            'day' => $day, 
            'location' => $entry->get('location'),
            'hours' => $entryStart->format('H:i') . '-' . $entryEnd->format('H:i')
        );
        $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
        if($this->daysOff->includes($date)){
            $row['fill'] = true;
        }
        else{
            $row['fill'] = false;
        }
        $this->rows->add($row);
    }
    
    private function addEmptyRow(int $day){
        $row = array('day' => $day);
        $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
        if($this->daysOff->includes($date)){
            $row['fill'] = true;
        }
        else{
            $row['fill'] = false;
        }
        $this->rows->add($row);
    }
    
    public function getRows() : Container {
        return $this->rows;
    }
    
    public function countTotalInspections() : int {
        $count = 0; 
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            if($entry->get('can_be_inspection') === 1){
                $count++;
            }
        }
        return $count;
    }
    
    public function countUndergroundInspections() : int {
        $count = 0; 
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            if($entry->get('can_be_inspection') === 1 && $entry->get('underground') === 1){
                $count++;
            }
        }
        return $count;
    }
    
    public function countSurfaceInspections() : int {
        $count = 0; 
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            if($entry->get('can_be_inspection') === 1 && $entry->get('underground') === 0){
                $count++;
            }
        }
        return $count;
    }
}

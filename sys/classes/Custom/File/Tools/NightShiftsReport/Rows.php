<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\File\Tools\NightShiftsReport;

use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Custom\File\Tools\Timesheets\TimesCalculator as TimesCalculator;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use Custom\Converters\Time as Time;
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
    private Container $periods;
    private Container $entries;
    private int $nightShiftRow;
    
    public function __construct(int $month, int $year) {
        $this->month = $month;
        $this->year = $year;
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $this->nightShiftRow = (int) $cfg->get('timesheets_night_shift_row_index');
        $this->loadUserPeriods();
        $this->loadEntries();
        $this->initRows();
    }
    
    private function loadUserPeriods() : void {
        $view = new UsersEmploymentPeriodsView();
        $this->periods = $view->getOrderedActiveByMonthAndYear($this->month, $this->year);
    }
    
    private function loadEntries() : void {
        $view = new ScheduleEntriesView();
        $start = new DateTime($this->year . '-' . $this->month . '-01');
        $start->modify('-1 days');
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        $end = new DateTime($this->year . '-' . $this->month . '-' . $lastDay . ' 23:59:59');
        $end->modify('+1 days');
        $this->entries = $view->getActive($start, $end);
    }
    
    private function initRows() : void {
        $this->rows = new Container();
        $lastDay = (int) date('t', strtotime($this->year . '-' . $this->month . '-1'));
        for($day = 1; $day <= $lastDay; $day++){
            $this->analizeEntries($day);
        }
    }
    
    private function analizeEntries(int $day) : void {
        foreach ($this->entries->toArray() as $item) {
            $entry = new Container($item);
            $this->analizeEntry($day, $entry);
        }
    }
    
    private function analizeEntry(int $day, Container $entry) : void {
        $username = $entry->get('username');
        $periods = $this->findUserPeriods($username);
        $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
        $entries = new Container();
        $entries->add($entry->toArray());
        $times = TimesCalculator::calculate($periods, $entries, $date, $date);
        $timesForDate = new Container($times->get($date->format('Y-m-d')));
        $nightShiftTime = $timesForDate->get($this->nightShiftRow);
        $row = array(
            'user' => $entry->get('name') . ' ' . $entry->get('surname'),
            'date' => $date->format('j-n-Y'),
            'location' => $entry->get('location'),
            'activity' =>$entry->get('activity_name'),
            'time' => Time::msToClockNoSeconds($nightShiftTime)
        );
        if($nightShiftTime > 0){
            $this->rows->add($row);
        }
    }
    
    private function findUserPeriods(string $username) : Container {
        $periods = new Container();
        foreach ($this->periods->toArray() as $item){
            $period = new Container($item);
            if($period->get('username') === $username){
                $periods->add($item);
            }
        }
        return $periods;
    }
    
    public function getRows() : Container {
        return $this->rows;
    }
}

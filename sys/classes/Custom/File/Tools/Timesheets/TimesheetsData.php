<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\OvertimeReductionDetailsView as OvertimeReductionDetailsView;
use Data\Access\Tables\UserTableDAO as UserTableDAO;
use Custom\File\Tools\Timesheets\DaysDetails as DaysDetails;
use Custom\File\Tools\Timesheets\DayDetails as DayDetails;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use DateTime;

/**
 * Description of TimesheetsData
 *
 * @author Tanzar
 */
class TimesheetsData {
    //--- data accessable with getters ---//
    private string $year;
    private string $month;
    private string $fullUsername;
    private int $workdaysCount;
    private int $userDailyJobTimeInMins;
    private int $previousOvertimeInMins;
    private DaysDetails $daysDetails;
    
    //--- internal not accessable data ---//
    private string $username;
    private Container $employmentPeriods;
    private Container $entries;
    
    public function __construct(int $month, int $year, string $username) {
        $languages = Languages::getInstance('polski');
        $months = new Container($languages->get('months'));
        $this->month = $months->get($month);
        $this->year = $year;
        $this->username = $username;
        $this->loadUserData($month, $year, $username);
        $this->initDaysDetails($month, $year);
        $this->initDailyJobTime($year, $month);
    }
    
    private function loadUserData(int $month, int $year, string $username) : void {
        $employmentsView = new UsersEmploymentPeriodsView();
        $this->employmentPeriods = $employmentsView->getActiveByUserMonthYear($username, $month, $year);
        $entriesView = new ScheduleEntriesView();
        $date = new DateTime($year . '-' . $month . '-1 23:59:59');
        $lastDate = new DateTime($date->format('Y-m-t H:i:s'));
        $this->entries = $entriesView->getActiveByUsernameToDate($username, $lastDate);
        $userDAO = new UserTableDAO();
        $userDetails = $userDAO->getByUsername($username);
        $this->fullUsername = $userDetails->get('surname') . ' ' . $userDetails->get('name');
    }
    
    private function initDaysDetails(int $month, int $year) : void {
        $this->daysDetails = new DaysDetails($this->username, $this->employmentPeriods, $this->entries);
        $date = new DateTime($year . '-' . $month . '-01');
        $date->modify('-1 days');
        $reduction = $this->getOvetimeReduction($month, $year);
        $overtime = $this->daysDetails->sumOvertime($date);
        $wzn = $this->daysDetails->sumWZN($date);
        $this->previousOvertimeInMins = floor((($overtime - $wzn - $reduction) / 1000) / 60);
        $this->workdaysCount = $this->daysDetails->sumWorkdays($year, $month);
    }
    
    private function getOvetimeReduction(int $month, int $year) : int {
        $monthStart = new DateTime($year . '-' . $month . '-01');
        $monthStart->modify('-1 month');
        $date = new DateTime($monthStart->format('Y-m-t'));
        $view = new OvertimeReductionDetailsView();
        $reductions = $view->getActiveByUsernameBeforeOrAt($this->username, $date);
        $value = 0;
        foreach ($reductions->toArray() as $item) {
            $reduction = new Container($item);
            $value += (int) $reduction->get('time');
        }
        return $value;
    }
    
    private function initDailyJobTime(int $year, int $month) : void {
        $monthStart = new DateTime($year . '-' . $month . '-01');
        $monthEnd = new DateTime($year . '-' . $month . '-' . $monthStart->format('t'));
        $time = 0;
        foreach ($this->employmentPeriods->toArray() as $item) {
            $period = new Container($item);
            $start = new DateTime($period->get('start'));
            $end = new DateTime($period->get('end'));
            if($monthStart <= $end && $monthEnd >= $start){
                $periodTime = $this->calcPeriodWorkTime($period);
                $time = max($time, $periodTime);
            } 
        }
        $this->userDailyJobTimeInMins = floor(($time / 1000) / 60);
    }
    
    private function calcPeriodWorkTime(Container $period) : int {
        $start = new DateTime($period->get('start') . ' ' . $period->get('standard_day_start'));
        $end = new DateTime($period->get('start') . ' ' . $period->get('standard_day_end'));
        if($end < $start){
            $end->modify('+1 days');
        }
        return (int) $end->format('Uv') - (int) $start->format('Uv');
    }
    
    public function getYear(): string {
        return $this->year;
    }

    public function getMonth(): string {
        return $this->month;
    }

    public function getFullUsername(): string {
        return $this->fullUsername;
    }

    public function getUserDailyJobTimeInMins(): int {
        return $this->userDailyJobTimeInMins;
    }

    public function getPreviousOvertimeInMins(): int {
        if($this->previousOvertimeInMins < 0){
            return 0;
        }
        return $this->previousOvertimeInMins;
    }

    public function getDayDetails(DateTime $date) : DayDetails {
        return $this->daysDetails->getDetails($date);
    }
    
    public function getWorkdaysCount(): int {
        return $this->workdaysCount;
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\DaysOffUserDetailsView as DaysOffUserDetailsView;
use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Data\Access\Views\InventoryLogDetailsView as InventoryLogDetailsView;
use Custom\File\Tools\Timesheets\TimesCalculator as TimesCalculator;
use Tanweb\Session as Session;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Dates\HolidayChecker as HolidayChecker;
use DateTime;

/**
 * Description of IndexService
 *
 * @author Tanzar
 */
class IndexService {
    
    public static function getDaysWithoutEntries() : Container {
        $entriesView = new ScheduleEntriesView();
        $employmentView = new UsersEmploymentPeriodsView();
        $username = Session::getUsername();
        $date = new DateTime();
        $employments = $employmentView->getByUsernameToDate($username, $date);
        $entries = $entriesView->getActiveByUsernameToDate($username, $date);
        return self::formDaysReport($employments, $entries);
    }
    
    private static function formDaysReport(Container $employments, Container $entries) : Container {
        $start = self::getEarliestStartDate($employments);
        $end = new DateTime();
        $daysOff = self::getDaysOff($start, $end);
        $daysTimes = TimesCalculator::calculate($employments, $entries, $start, $end);
        return self::parseDaysTimes($daysOff, $employments, $daysTimes);
    }
    
    private static function getEarliestStartDate(Container $employments) : DateTime {
        $start = new DateTime();
        foreach ($employments->toArray() as $item) {
            $period = new Container($item);
            $periodStart = new DateTime($period->get('start') . ' 00:00:01');
            if($periodStart < $start){
                $start = $periodStart;
            }
        }
        return $start;
    }
    
    private static function getDaysOff(DateTime $start, DateTime $end) : Container {
        $result = self::getUserDaysOffDates();
        $current = new DateTime($start->format('Y-m-d'));
        while($current <= $end){
            $weekday = (int) $current->format('N');
            if($weekday === 6 || $weekday === 7 || HolidayChecker::isHoliday($current)){
                if(!$result->contains($current->format('Y-m-d'))){
                    $result->add($current->format('Y-m-d'));
                }
            }
            $current->modify('+1 days');
        }
        return $result;
    }
    
    private static function getUserDaysOffDates() : Container {
        $username = Session::getUsername();
        $view = new DaysOffUserDetailsView();
        $userSpecificDays = $view->getActiveByUsername($username);
        $result = new Container();
        foreach ($userSpecificDays->toArray() as $item) {
            $day = new Container($item);
            if(!$result->contains($day->get('days_off_date'))){
                $result->add($day->get('days_off_date'));
            }
        }
        $dao = new DaysOffDAO();
        $daysOffForAll = $dao->getActiveForAll();
        foreach ($daysOffForAll->toArray() as $item) {
            $day = new Container($item);
            if(!$result->contains($day->get('date'))){
                $result->add($day->get('date'));
            }
        }
        return $result;
    }
    
    private static function parseDaysTimes(Container $daysOff, Container $employments, Container $daysTimes) : Container{
        $result = new Container();
        foreach ($employments->toArray() as $item) {
            $period = new Container($item);
            $current = new DateTime($period->get('start'));
            $end = new DateTime($period->get('end'));
            $standardDayTime = self::getStandardDayTime($period);
            while($current <= $end && $current <= new DateTime()){
                $dayTime = self::summarizeDate($current, $daysTimes);
                if($dayTime < $standardDayTime && !$daysOff->contains($current->format('Y-m-d'))){
                    $result->add($current->format('d-m-Y'));
                }
                $current->modify('+1 days');
            }
        }
        return $result;
    }
    
    private static function getStandardDayTime(Container $employment) : int {
        $start = new DateTime($employment->get('start') . ' ' . $employment->get('standard_day_start'));
        $end = new DateTime($employment->get('start') . ' ' . $employment->get('standard_day_end'));
         if($end < $start) {
             $end->modify('+1 days');
         }
         return (int) $end->format('Uv') - (int) $start->format('Uv');
    }
    
    private static function summarizeDate(DateTime $date, Container $daysTimes) : int {
        $appConfig = AppConfig::getInstance();
        $cfg = $appConfig->getAppConfig();
        $nsRow = $cfg->get('timesheets_night_shift_row_index');
        $times = $daysTimes->get($date->format('Y-m-d'));
        $sum = 0;
        foreach ($times as $row => $value) {
            if($row !== $nsRow){
                $sum += (int) $value;
            }
        }
        return $sum;
    }
    
    public static function getUnconfirmedEquipment() : Container {
        $view = new InventoryLogDetailsView();
        $username = Session::getUsername();
        return $view->getUnconfirmedForUser($username);
    }
}

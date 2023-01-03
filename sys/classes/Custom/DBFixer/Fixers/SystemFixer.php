<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\DBFixer\Fixers;

use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\DBFixer\FixerReport as FixerReport;
use Data\Access\Tables\PrivilageTableDAO as PrivilageTableDAO;
use Data\Access\Tables\DaysOffUserDAO as DaysOffUserDAO;
use Data\Access\Views\DaysOffUserDetailsView as DaysOffUserDetailsView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Tables\LocationDAO as LocationDAO;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use DateTime;

/**
 * Description of SystemFixer
 *
 * @author Tanzar
 */
class SystemFixer {
    
    public static function run(FixerReport $report) : void {
        self::fixPrivilages($report);
        self::fixDaysOff($report);
        self::fixLocationsTable($report);
        self::fixLocationGroupsTable($report);
    }
    
    private static function fixPrivilages(FixerReport $report) : void {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getSecurity();
        $availablePrivilages = new Container($cfg->get('privilages'));
        $dao = new PrivilageTableDAO();
        $privilages = $dao->getAll();
        foreach ($privilages->toArray() as $item) {
            $privilage = new Container($item);
            $name = $privilage->get('privilage');
            if(!$availablePrivilages->contains($name) && $name !== 'admin'){
                $id = (int) $privilage->get('id');
                $dao->remove($id);
                $report->addDisabled();
            }
        }
    }
    
    private static function fixDaysOff(FixerReport $report) : void {
        $view = new DaysOffUserDetailsView();
        $userEmploymentPeriodsView = new UsersEmploymentPeriodsView();
        $dao = new DaysOffUserDAO();
        $employments = $userEmploymentPeriodsView->getAll();
        $usersDays = $view->getAll();
        foreach ($usersDays->toArray() as $i => $item) {
            $userDayDetails = new Container($item);
            if(self::isNotInEmployment($userDayDetails, $employments)){
                $id = (int) $userDayDetails->get('id');
                $dao->remove($id);
                $report->addRemoved();
            }
        }
    }
    
    private static function isNotInEmployment(Container $userDayDetails, Container $employments) : bool {
        $date = new DateTime($userDayDetails->get('days_off_date'));
        $username = $userDayDetails->get('username');
        foreach ($employments->toArray() as $item) {
            $period = new Container($item);
            if($period->get('username') === $username){
                $start = new DateTime($period->get('start'));
                $end = new DateTime($period->get('end'));
                if($date >= $start && $date <= $end){
                    return false;
                }
            }
        }
        return true;
    }
    
    private static function fixLocationsTable(FixerReport $report) : void {
        $dao = new LocationDAO();
        $locations = $dao->getAll();
        foreach ($locations->toArray() as $item) {
            $location = new Container($item);
            $changes = 0;
            $changes += self::fixDates($location, $report);
            $changes += self::fixActive($location, $report);
            if($changes > 0) {
                $dao->save($location);
            }
        }
    }
    
    private static function fixLocationGroupsTable(FixerReport $report) : void {
        $dao = new LocationGroupDAO();
        $groups = $dao->getAll();
        foreach ($groups->toArray() as $item) {
            $group = new Container($item);
            $changes = 0;
            $changes += self::fixDates($group, $report);
            $changes += self::fixActive($group, $report);
            if($changes > 0) {
                $dao->save($group);
            }
        }
    }
    
    private static function fixDates(Container $entry, FixerReport $report) : int {
        $start = new DateTime($entry->get('active_from'));
        $end = new DateTime($entry->get('active_to'));
        if($start > $end){
            $entry->add($end->format('Y-m-d'), 'active_from', true);
            $entry->add($start->format('Y-m-d'), 'active_to', true);
            $report->addDatesChanged();
            return 1;
        }
        return 0;
    }
    
    private static function fixActive(Container $entry, FixerReport $report) : int {
        $start = new DateTime($entry->get('active_from') . ' 01:00:00');
        $end = new DateTime($entry->get('active_to') . ' 01:00:00');
        $today = new DateTime(date('Y-m-d') . ' 01:00:00');
        $active = $entry->get('active');
        if($active && ($today < $start || $today > $end)){
            $entry->add(0, 'active', true);
            $report->addDisabled();
            return 1;
        }
        elseif (!$active && $today >= $start && $today <= $end) {
            $entry->add(1, 'active', true);
            $report->addEnabled();
            return 1;
        }
        return 0;
    }
}

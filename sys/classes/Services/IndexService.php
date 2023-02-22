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
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\SuspensionDecisionDetailsView as SuspensionDecisionDetailsView;
use Custom\File\Tools\Timesheets\DaysDetails as DaysDetails;
use Custom\File\Tools\Timesheets\DayDetails as DayDetails;
use Tanweb\Session as Session;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Dates\HolidayChecker as HolidayChecker;
use Custom\Converters\Time as Time;
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
        return self::formDaysReport($username, $employments, $entries);
    }
    
    private static function formDaysReport(string $username, Container $employments, Container $entries) : Container {
        $daysDetails = new DaysDetails($username, $employments, $entries);
        $result = new Container();
        $today = new DateTime();
        foreach ($daysDetails->toArray() as $key => $details) {
            $sum = $details->sumTimes();
            $workdayTime = $details->getStandardWorkdayTime();
            if($today >= new DateTime($key) && $details->getDayLetter() === 'P' && $sum < $workdayTime){
                $text = $key . ' - ';
                $text .= Time::msToClockNoSeconds($details->sumTimes()) . ' / ';
                $text .= Time::msToClockNoSeconds($details->getStandardWorkdayTime());
                $result->add($text);
            }
        }
        return $result;
    }
    
    public static function getUnconfirmedEquipment() : Container {
        $view = new InventoryLogDetailsView();
        $username = Session::getUsername();
        return $view->getUnconfirmedForUser($username);
    }
    
    public static function getUnassignedDecisions() : Container {
        $view = new DecisionDetailsView();
        $username = Session::getUsername();
        $decisions = $view->getActiveRequiringSuspensionByUsername($username);
        $connectionsView = new SuspensionDecisionDetailsView();
        $connections = $connectionsView->getActiveByUsername($username);
        $result = new Container();
        foreach ($decisions->toArray() as $item) {
            $decision = new Container($item);
            $notFound = true;
            foreach ($connections->toArray() as $connection) {
                if($connection['id'] === $decision->get('id')){
                    $notFound = false;
                }
            }
            if($notFound){
                $result->add($decision->get('date') . ' : ' . $decision->get('document_number'));
            }
        }
        return $result;
    }
}

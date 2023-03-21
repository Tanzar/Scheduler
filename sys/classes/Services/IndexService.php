<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Views\LocationDetailsView as LocationDetailsView;
use Data\Access\Views\ScheduleEntriesView as ScheduleEntriesView;
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Views\InventoryLogDetailsView as InventoryLogDetailsView;
use Data\Access\Views\DecisionDetailsView as DecisionDetailsView;
use Data\Access\Views\SuspensionDecisionDetailsView as SuspensionDecisionDetailsView;
use Custom\File\Tools\Timesheets\DaysDetails as DaysDetails;
use Custom\Reports\ScheduleReport as ScheduleReport;
use Custom\Reports\InspectorReport as InspectorReport;
use Custom\Blockers\ScheduleBlocker as ScheduleBlocker;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;
use Tanweb\Security\Security as Security;
use Tanweb\Session as Session;
use Tanweb\Container as Container;
use Custom\Converters\Time as Time;
use DateTime;

/**
 * Description of IndexService
 *
 * @author Tanzar
 */
class IndexService {
    
    public static function getScheduleBlockerDate() : DateTime {
        $blocker = new ScheduleBlocker();
        $date = $blocker->getNextBLockerDate();
        return $date;
    }
    
    public static function getInspectorBlockerDate() : DateTime {
        $blocker = new InspectorDateBlocker();
        $date = $blocker->getNextBLockerDate();
        return $date;
    }
    
    public static function getLocationsInTemporatyGroups() : Container {
        $view = new LocationDetailsView();
        $locations = $view->getInTemporaryGroup();
        $result = new Container();
        foreach ($locations->toArray() as $item) {
            $location = new Container($item);
            $result->add($location->get('name'));
        }
        return $result;
    }
    
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
    
    public static function getUserData(int $year, string $username) : Container {
        $result = new Container();
        $security = Security::getInstance();
        $schedulePrivilages = new Container(['admin', 'schedule_user', 'schedule_user_inspector', 'schedule_admin']);;
        if($security->userHaveAnyPrivilage($schedulePrivilages)){
            $result->add(ScheduleReport::generate($year, $username), 'schedule');
        }
        $inspectorPrivilages = new Container(['admin', 'schedule_user_inspector']);
        if($security->userHaveAnyPrivilage($inspectorPrivilages)){
            $result->add(InspectorReport::generate($year, $username)->toArray(), 'inspector');
        }
        return $result;
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Custom\File\AttendanceList as AttendanceList;
use Custom\File\NotificationList as NotificationList;
use Custom\File\Timesheets as Timesheets;
use Custom\File\Workcard as Workcard;
use Custom\File\NightShiftsReport as NightShiftsReport;
use Custom\File\InstrumentUsageCard as InstrumentUsageCard;
use Tanweb\Session as Session;

/**
 * Description of PrintsService
 *
 * @author Tanzar
 */
class PrintsService {
    
    public function generateAttendanceList(int $month, int $year) : void {
        AttendanceList::generate($month, $year);
    }
    
    public function generateNotificationList(int $month, int $year) : void {
        NotificationList::generate($month, $year);
    }
    
    public function generateTimesheetsForUser(string $username, int $month, int $year) : void {
        Timesheets::generate($month, $year, $username);
    }
    
    public function generateTimesheetsForCurrentUser(int $month, int $year) : void {
        $username = Session::getUsername();
        Timesheets::generate($month, $year, $username);
    }
    
    public function generateWorkcardForUser(string $username, int $month, int $year) : void {
        Workcard::generate($month, $year, $username);
    }
    
    public function generateWorkcardForCurrentUser(int $month, int $year) : void {
        $username = Session::getUsername();
        Workcard::generate($month, $year, $username);
    }
    
    public function generateNightShiftReport(int $month, int $year) : void {
        NightShiftsReport::generate($month, $year);
    }
    
    public function generateInstrumentUsageCard(int $instrumentId, int $year) : void {
        InstrumentUsageCard::generate($instrumentId, $year);
    }
}

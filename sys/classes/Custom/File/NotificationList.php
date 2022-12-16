<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\Config\INI\Languages as Languages;
use Custom\Dates\HolidayChecker as HolidayChecker;
use Services\UserService as UserService;
use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of NotificationList
 *
 * @author Tanzar
 */
class NotificationList extends PDFMaker{
    private int $month;
    private int $year;
    private array $months;
    private array $weekdays;
    private int $defaultMargins = 5;
    private int $defaultColWidth = 20;
    private int $dayRowHeight = 16;
    private int $usersPerPageLimit;
    private int $firstColWidth = 6;
    private int $daysPerPageLimit;
    private Container $usersSets;
    private string $title;
    private Container $daysOff;
    
    private function __construct(int $month, int $year) {
        $this->month = $month;
        $this->year = $year;
        $languages = Languages::getInstance();
        $this->months = $languages->get('months');
        $this->weekdays = $languages->get('weekdays');
        $this->usersSets = new Container();
        parent::__construct('L', 'A3');
        $this->SetFillColor(200, 200, 200);
        $this->setCurrentSize(8);
        $this->setMargin('all', $this->defaultMargins);
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $this->SetAuthor($cfg->get('name') . ' web app');
        $this->title = 'Lista Zgłoszenia ' . $this->months[$month] . ' ' . $year;
        $this->SetTitle($this->title, true);
        $this->countDaysPerPageLimit();
        $this->loadDaysOff($month, $year);
    }
    
    private function loadDaysOff(int $month, int $year) : void {
        $dao = new DaysOffDAO();
        $data = $dao->getActiveForAllByMonthAndYear($month, $year);
        $this->daysOff = new Container();
        foreach ($data->toArray() as $item) {
            $day = new Container($item);
            $date = $day->get('date');
            $this->daysOff->add($date);
        }
    }
    
    private function countDaysPerPageLimit() : void {
        $pageHeight = $this->h;
        $heightLimit = $pageHeight - ( 2 * $this->defaultMargins) - 15;
        $this->daysPerPageLimit = floor($heightLimit / $this->dayRowHeight);
    }
    
    public static function generate(int $month, int $year) : void {
        $pdf = new NotificationList($month, $year);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    protected function getTitle() : string {
        return $this->title;
    }
    
    protected function print() : void {
        $this->getUsers();
        for($i = 0 ; $i < $this->usersSets->length(); $i++){
            $set = $this->usersSets->get($i);
            $this->printPagesPerSet($set);
        }
    }
    
    private function getUsers() : void {
        $users = $this->loadUsers();
        $pageWidth = $this->w;
        $widthLimit = $pageWidth - ( 2 * $this->defaultMargins) - $this->firstColWidth;
        $this->usersPerPageLimit = floor($widthLimit / $this->defaultColWidth);
        $this->splitUsersToSets($users, $this->usersPerPageLimit);
    }
    
    private function loadUsers() : Container {
        $service = new UserService();
        return $service->getEmployedUsersListByMonthOrdered($this->month, $this->year);
    }
    
    private function splitUsersToSets(Container $users, int $limitPerPage) : void {
        $this->usersSets = new Container();
        $setsCount = ceil($users->length() / $limitPerPage);
        $index = 0;
        for($setIndex = 0; $setIndex < $setsCount ; $setIndex++){
            $set = new Container();
            $lastIndex = min(($users->length() - $index), $limitPerPage) + $index;
            for($index; $index < $lastIndex; $index++){
                $set->add($users->get($index));
            }
            $this->usersSets->add($set);
        }
    }
    
    private function printPagesPerSet(Container $set) : void {
        $monthStart = date($this->year . '-' . $this->month . '-1');
        $daysCount = (int) date("t", strtotime($monthStart));
        $pagesPerUserSet = ceil($daysCount / $this->daysPerPageLimit);
        $lastPageNumber = $this->usersSets->length() * $pagesPerUserSet;
        for($daysSet = 0 ; $daysSet < $pagesPerUserSet ; $daysSet++){
            $this->printPagePerDaysSet($set, $daysSet, $daysCount);
            if( (int) $this->PageNo() !== (int) $lastPageNumber){
                $this->AddPage();
            }
        }
    }
    
    private function printPagePerDaysSet(Container $set, int $daysSet, int $daysCount) : void {
        $startDay = 1 + ($daysSet * $this->daysPerPageLimit);
        $endDay = min(($startDay + $this->daysPerPageLimit - 1), $daysCount);
        $this->printPage($set, $startDay, $endDay);
    }
    
    private function printPage(Container $usersSet, int $startDay, int $endDay) : void {
        $width = $this->CalculateTotalWidth($usersSet);
        $text = $this->months[$this->month] . ' ' . $this->year;
        $this->setY($this->defaultMargins);
        $this->setX($this->defaultMargins);
        $this->writeCell($width, 5, $text, 1, 'C');
        $this->newLine(5);
        $this->printUsersSet($usersSet);
        $this->printDaysTable($startDay, $endDay, $usersSet->length());
    }
    
    private function CalculateTotalWidth(Container $set) : int {
        $totalWidth = $this->firstColWidth + ($this->defaultColWidth * $set->length());
        return $totalWidth;
    }
    
    private function printUsersSet(Container $set) : void {
        $columns = $this->generateColumnsSettings($set->length());
        $data = $this->prepareUserNames($set);
        $this->makeTable($columns, $data, 1);
    }
    
    private function generateColumnsSettings(int $usersCount) : Columns {
        $columns = new Columns();
        $columns->add(new Column($this->firstColWidth, 'empty'));
        for($i = 0; $i < $usersCount; $i++){
            $columns->add(new Column($this->defaultColWidth, $i));
        }
        return $columns;
    }
    
    private function prepareUserNames(Container $set) : Container {
        $result = new Container();
        $firstRow = array('empty' => '');
        $secondRow = array('empty' => '');
        for($i = 0 ; $i < $set->length() ; $i++){
            $item = $set->get($i);
            $user = new Container($item);
            $firstRow[$i] = $user->get('name');
            $secondRow[$i] = $user->get('surname');
        }
        $result->add($firstRow);
        $result->add($secondRow);
        return $result;
    }
    
    private function printDaysTable(int $startDay, int $endDay, int $usersCount) : void {
        $columns = $this->generateDayColumnsSettings($usersCount);
        $data = new Container();
        $rowsToFill = new Container();
        for($day = $startDay ; $day <= $endDay ; $day++){
            $data->add(array('day' => $day));
            if($this->isDayOff($day)){
                $rowsToFill->add($day - $startDay);
            }
        }
        $this->makeTable($columns, $data, 1, $this->dayRowHeight, $rowsToFill);
    }
    
    private function generateDayColumnsSettings(int $usersCount) : Columns {
        $columns = new Columns();
        $columns->add(new Column($this->firstColWidth, 'day'));
        for($i = 0; $i < $usersCount; $i++){
            $columns->add(new Column($this->defaultColWidth, 'empty'));
        }
        return $columns;
    }
    
    private function isDayOff(int $day) : bool {
        $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
        $weekday = (int) $date->format('N');
        if($weekday === 6 || $weekday === 7){
            return true;
        }
        if(HolidayChecker::isHoliday($date)){
            return true;
        }
        if($this->daysOff->contains($date->format('Y-m-d'))){
            return true;
        }
        return false;
    }
    
    function footer() {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $appname = $cfg->get('name');
        $y = $this->h - 5;
        $fontSize = $this->getCurrentFontSize();
        $this->setCurrentSize(8);
        $this->SetY($y);
        $this->writeCell(0, 5, "Plik wygenerowany przez system " . $appname . ", strona " . $this->PageNo() . "/{nb}", 0, 'R');
        $this->setCurrentSize($fontSize);
    }
}

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
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Data\Access\Tables\DaysOffDAO as DaysOffDAO;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of AttendanceList
 *
 * @author Tanzar
 */
class AttendanceList extends PDFMaker{
    private int $month;
    private int $year;
    private array $months;
    private array $weekdays;
    private int $pageCount;
    private int $defaultBottomMargin;
    private int $signatureColWidth;
    private Container $users;
    private string $title;
    private Container $daysOff;
    
    private function __construct(int $month, int $year) {
        $this->month = $month;
        $this->year = $year;
        $languages = Languages::getInstance();
        $this->months = $languages->get('months');
        $this->weekdays = $languages->get('weekdays');
        $this->pageCount = 1;
        $this->defaultBottomMargin = 15;
        $this->signatureColWidth = 22;
        parent::__construct('L', 'A3');
        $this->SetFillColor(200, 200, 200);
        $this->setCurrentSize(9);
        $this->setMargin('left', 3);
        $this->setMargin('bottom', 15);
        $this->SetAuthor('Scheduler web app');
        $this->title = 'Lista Obecności ' . $this->months[$month] . ' ' . $year;
        $this->SetTitle($this->title, true);
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
    
    public static function generate(int $month, int $year) : void {
        $pdf = new AttendanceList($month, $year);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    protected function getTitle() : string {
        return $this->title;
    }
    
    function Header() {
        $this->SetFont('arialpl','',10);
        $this->setY(5);
        $text = 'Lista obecności pracowników Okręgowego Urzędu Górniczego w Rybniku '
                . $this->months[$this->month] . ' ' . $this->year . ' strona: ' 
                . $this->pageCount;
        $this->Cell(0,0,$text,0,0,'C');
        $this->Ln(3);
        $this->SetX(3);
        $this->pageCount++;
        $this->loadUsers();
    }
    
    private function loadUsers() : void {
        $view = new UsersEmploymentPeriodsView();
        $users = $view->getOrderedActiveByMonthAndYear($this->month, $this->year);
        $addedUsernames = new Container();
        $data = new Container();
        $i = 0;
        foreach ($users->toArray() as $item) {
            $user = new Container($item);
            $username = $user->get('username');
            if(!$addedUsernames->contains($username)){
                $addedUsernames->add($username);
                $data->add(array(
                    'id' => $i + 1,
                    'name' => $user->get('name'),
                    'surname' => $user->get('surname')
                ));
                $i++;
            }
        }
        $this->users = $data;
    }
    
    protected function print() : void {
        $daysLimit = (int) cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
        $this->printHeadersSet(1, 16);
        $this->printDataUsersRows(1, 16);
        $this->printSignaruresRow(1, 16);
        $this->generateTextUnderTable();
        $this->AddPage();
        $this->printHeadersSet(17, $daysLimit);
        $this->printDataUsersRows(17, $daysLimit);
        $this->printSignaruresRow(17, $daysLimit);
        $this->generateTextUnderTable();
        $this->printSystemMark();
    }
    
    private function printHeadersSet(int $startDay, int $endDay) : void {
        $columns = $this->generateHeadersColumns($startDay, $endDay);
        $data = $this->generateHeadersSetData($startDay, $endDay);
        $this->makeTable($columns, $data);
    }
    
    private function generateHeadersColumns(int $startDay, int $endDay) : Columns {
        $columns = new Columns();
        $columns->add(new Column(7, 'lp'));
        $columns->add(new Column(20, 'empty'));
        $columns->add(new Column(30, 'empty'));
        for($i = $startDay ; $i <= $endDay; $i++){
            $fill = false;
            if($this->isDayOff($i)){
                $fill = true;
            }
            $columns->add(new Column($this->signatureColWidth, $i, $fill));
        }
        return $columns;
    }
    
    private function generateHeadersSetData(int $startDay, int $endDay) : Container {
        $data = new Container();
        $firstRow = array(
            'lp' => 'Lp'
        );
        for($i = $startDay ; $i <= $endDay; $i++){
            $firstRow[$i] = $i;
        }
        $data->add($firstRow);
        $secondRow = array();
        for($i = $startDay ; $i <= $endDay; $i++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $i);
            $dayName = $this->weekdays[$date->format('N')];
            $secondRow[$i] = $dayName;
        }
        $data->add($secondRow);
        return $data;
    }
    
    private function printDataUsersRows(int $startDay, int $endDay) : void {
        $data = $this->users;
        $columns = $this->generateTableDataConfig($startDay, $endDay);
        $this->makeTable($columns, $data, 1);
    }
    
    private function generateTableDataConfig(int $startDay, int $endDay) : Columns {
        $columns = new Columns();
        $columns->add(new Column(7, 'id'));
        $name = new Column(20, 'name');
        $name->setAlignLeft();
        $columns->add($name);
        $surname = new Column(30, 'surname');
        $surname->setAlignLeft();
        $columns->add($surname);
        for($i = $startDay ; $i <= $endDay; $i++){
            $fill = false;
            if($this->isDayOff($i)){
                $fill = true;
            }
            $columns->add(new Column(6, 'noKey', $fill));
            $columns->add(new Column(16, 'noKey', $fill));
        }
        return $columns;
    }
    
    private function printSignaruresRow(int $startDay, int $endDay) : void {
        $usersCount = $this->users->length();
        if($usersCount % 52 === 0){
            $this->defaultBottomMargin = 14;
            $this->setMargin('bottom', 14);
        }
        $data = $this->generateSignatureRow();
        $columns = $this->generateSignaturesConfig($startDay, $endDay);
        $this->makeTable($columns, $data, 1);
    }
    
    private function generateSignaturesConfig(int $startDay, int $endDay) : Columns {
        $columns = new Columns();
        $col = new Column(57, 'title');
        $col->setAlignLeft();
        $columns->add($col);
        for($i = $startDay ; $i <= $endDay; $i++){
            $fill = false;
            if($this->isDayOff($i)){
                $fill = true;
            }
            $columns->add(new Column(22, 'noKey', $fill));
            
        }
        return $columns;
    }
    
    private function generateSignatureRow() {
        $data = new Container();
        $data->add(array(
            'title' => 'Podpis Dyrektora'
        ));
        return $data;
    }
    
    private function generateTextUnderTable() : void {
        $this->setMargin('bottom', 0);
        $this->writeCell(30, 5, 'Razem spóźnienia');
        $this->writeCell(0, 5, 'usprawiedliwione         -min.');
        $this->Ln();
        $this->writeCell(30, 5, '');
        $this->writeCell(0, 5, 'nieusprawiedliwione      -min.');
        $this->setMargin('bottom', $this->defaultBottomMargin);
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
    
    private function printSystemMark() {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $appname = $cfg->get('name');
        $y = $this->h - 5;
        $this->setCurrentSize(8);
        $this->SetY($y);
        $this->SetAutoPageBreak(false);
        $this->writeCell(0, 5, 'Plik wygenerowany przez system ' . $appname, 0, 'R');
    }
}

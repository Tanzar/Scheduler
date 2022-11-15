<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Custom\File\Tools\NightShiftsReport\Rows as Rows;
use Data\Access\Tables\NightShiftReportNumberDAO as NightShiftReportNumberDAO;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\INI\AppConfig as AppConfig;
use DateTime;

/**
 * Description of NightShiftsReport
 *
 * @author Tanzar
 */
class NightShiftsReport extends PDFMaker{
    private string $title;
    private int $month;
    private array $months;
    private array $monthsWhen;
    private array $monthsWhich;
    private int $year;
    private int $margin;
    private int $bottomMargin;
    private int $font;
    private Container $config;
    private Rows $rows;
    
    
    private function __construct(int $month, int $year) {
        parent::__construct('P', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->font = 12;
        $this->setCurrentSize($this->font);
        $this->margin = 10;
        $this->bottomMargin = 15;
        $this->setMargin('all', $this->margin);
        $this->setMargin('bottom', $this->bottomMargin);
        $this->SetAuthor('Scheduler web app');
        $this->month = $month;
        $this->year = $year;
        $this->rows = new Rows($month, $year);
        $appconfig = AppConfig::getInstance();
        $this->config = $appconfig->getAppConfig();
        $languages = Languages::getInstance();
        $this->months = $languages->get('months');
        $this->monthsWhen = $languages->get('months_when');
        $this->monthsWhich = $languages->get('months_which');
        $this->title = 'Raport nocnej zmiany za ' . $this->months[$month] . ' ' . $year;
        $this->SetTitle($this->title, true);
    }
    
    public static function generate(int $month, int $year) : void {
        $pdf = new NightShiftsReport($month, $year);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    private function print() : void {
        $this->printHeaders();
        $this->printTopic();
        $this->printTableHeaders();
        $this->printTable();
        $this->printFoot();
        $this->printSystemMark();
    }
    
    private function printHeaders() : void {
        $this->SetY(31);
        $this->writeCell(50, 5, $this->getDocumentNumber());
        $nextMonthDate = new DateTime($this->year . '-' . $this->month . '-01');
        $nextMonthDate->modify('+1 month');
        $nextMonth = (int) $nextMonthDate->format('m');
        $text = $this->config->get('location') . ', dnia         ' . 
                $this->monthsWhich[$nextMonth] . ' ' . $this->year . 'r.';
        $this->writeCell(0, 5, $text, 0, 'R');
        $this->Ln(10);
        $this->writeCell(0, 5, 'L.dz.');
        $this->setMargin('left', 118.4);
        $this->Ln(25);
        $this->writeCell(0, 5, 'Wyższy Urząd Górniczy');
        $this->Ln(5);
        $this->writeCell(0, 5, 'w Katowicach');
        $this->Ln(5);
        $this->writeCell(0, 5, 'Biuro Budżetowo - Finansowe');
        $this->setMargin('left', $this->margin);
        $this->Ln(25);
    }
    
    private function getDocumentNumber() : string {
        $dao = new NightShiftReportNumberDAO();
        $numbers = $dao->getByYear($this->year);
        if($numbers->length() === 0){
            return 'Nie określony, poinformuj admina';
        }
        else{
            return $numbers->get('number');
        }
    }
    
    private function printTopic() : void {
        $this->writeCell(23, 5, 'Dotyczy:');
        $text = 'informacji na temat liczby godzin przepracowanych przez '
                . 'pracowników inspekcyjno - technicznych tut. '
                . 'Urzędu na zmianie nocnej w miesiącu ' 
                . $this->monthsWhen[$this->month] . ' ' . $this->year . 'r.';
        $this->writeMulticell(0, 5, $text);
        $this->Ln(19);
    }
    
    private function printTableHeaders() : void {
        $this->writeCell(40, 14, 'Imię i Nazwisko', 1, 'C');
        $this->writeCell(20, 14, 'Data', 1, 'C');
        $this->writeCell(55, 14, 'Kopalnia', 1, 'C');
        $this->writeCell(60, 14, 'Protokół', 1, 'C');
        $this->writeMulticell(20, 7, 'Liczba godzin', 1, 'C');
    }
    
    private function printTable() : void {
        $this->setCurrentSize(10);
        $columns = $this->generateConfigForTable();
        $data = $this->rows->getRows();
        $this->makeTable($columns, $data);
        $this->setCurrentSize($this->font);
    }
    
    private function generateConfigForTable() : Columns {
        $columns = new Columns();
        $user = new Column(40, 'user');
        $columns->add($user);
        $date = new Column(20, 'date');
        $columns->add($date);
        $location = new Column(55, 'location');
        $columns->add($location);
        $activity = new Column(60, 'activity');
        $columns->add($activity);
        $hours = new Column(20, 'time');
        $columns->add($hours);
        return $columns;
    }
    
    private function printFoot() : void {
        $this->setMargin('bottom', 0);
        $this->setCurrentSize(10);
        $y = $this->GetY();
        $newY = $this->h - 39;
        if($newY < $y){
            $newY = $y;
        }
        $this->SetY($newY);
        $this->writeCell(0, 5, 'Otrzymują:');
        $this->Ln(5);
        $this->writeCell(0, 5, '1)   adresat;');
        $this->Ln(5);
        $this->writeCell(0, 5, '2)   OUG a/a');
        $this->Ln(5);
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
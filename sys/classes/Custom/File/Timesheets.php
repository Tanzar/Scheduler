<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Custom\File\Tools\Timesheets\Settings as Settings;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Converters\Time as Time;
use DateTime;

/**
 * Description of Timesheets
 *
 * @author Tanzar
 */
class Timesheets extends PDFMaker{
    private string $title;
    private Settings $data;
    private int $leftMargin;
    private int $font;
    
    private function __construct(int $month, int $year, string $username) {
        parent::__construct('L', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->font = 8;
        $this->setCurrentSize($this->font);
        $this->leftMargin = 10;
        $this->setMargin('all', $this->leftMargin);
        $this->SetAuthor('Scheduler web app');
        $this->data = new Settings($month, $year, $username);
        $languages = Languages::getInstance();
        $months = $languages->get('months');
        $this->title = 'Miesięczna ewidencja czasu pracy za ' . 
                $months[$month] . ' ' . $year . ' dla ' . $this->data->getFullUserName();
        $this->SetTitle($this->title, true);
    }
    
    public static function generate(int $month, int $year, string $username) : void {
        $pdf = new Timesheets($month, $year, $username);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    protected function getTitle() : string {
        return $this->title;
    }
    
    protected function print() : void {
        $this->printTopHeaders();
        $this->printTimePerDay();
        $this->printTimesTable();
        $this->printWatchtimeAndNightShift();
        $this->printSummary();
        $this->printFooter();
    }
    
    private function printTopHeaders() : void {
        $this->printFirstRowHeader();
        $this->writeCell(46, 26, '', 1);
        $this->Ln(8);
        $this->printSecondRowHeaders();
        $this->printAsMultiCell(34, 4.5, 'Liczba godzin przeniesionych z poprzedniego okresu rozliczeniowego');
        $this->writeCell(11, 8, '', 1);
        $this->printAsMultiCell(25, 8, $this->data->getOrganization());
        $this->Ln(8);
        $this->printThirdRowHeaders();
        $this->Ln(5);
        $this->printFourthRowsHeaders();
    }
    
    private function printFirstRowHeader() : void {
        $this->writeCell(81, 8, 'KARTA MIESIĘCZNEJ EWIDENCJI CZASU PRACY', 1, 'C');
        $this->writeCell(50, 8, 'Nazwisko i Imię pracownika:', 1, 'C');
        $this->writeCell(75, 8, $this->data->getFullUserName(), 1, 'C');
        $this->printAsMultiCell(25, 4, 'Nazwa jednostki organizacyjnej');
    }
    
    private function printSecondRowHeaders() : void {
        $this->writeCell(48.5, 8, 'ROK:', 1, 'C');
        $this->writeCell(32.5, 8, $this->data->getYear(), 1, 'C');
        $this->writeCell(30, 8, 'Symbol pracownika', 1, 'C');
        $this->printAsMultiCell(15, 4, 'Wymiar etatu');
        $this->printAsMultiCell(35, 4, 'Norma czasu pracy w okresie rozliczeniowym');
    }
    
    private function printThirdRowHeaders() : void {
        $this->writeCell(48.5, 10, 'MIESIĄC:', 1, 'C');
        $this->writeCell(32.5, 10, $this->data->getMonth(), 1, 'C');
        $this->writeCell(30, 10, '', 1, 'C');
        $this->writeCell(15, 10, Time::msToFullHours($this->data->getStandardFullTime()), 1, 'C');
        $tmpX = $this->GetX();
        $this->writeCell(17.5, 5, 'dni', 1, 'C');
        $this->writeCell(17.5, 5, 'godziny', 1, 'C');
        $this->SetX($tmpX + 35 + 34);
        $overtime = $this->data->getPreviousOvertime();
        $this->writeCell(11, 10, Time::msToClockNoSeconds($overtime), 1, 'C');
        $this->writeCell(25, 10, '', 1, 'C');
        $this->Ln(5);
        $this->SetX($tmpX);
        $workDays = $this->data->countWorkDays();
        $this->writeCell(17.5, 5, $workDays, 1, 'C');
        $hours = Time::msToClockNoSeconds($this->data->summarizeRow('standardWorkHours'));
        $this->writeCell(17.5, 5, $hours, 1, 'C');
    }
    
    private function printFourthRowsHeaders() : void {
        $this->printFourthRowsFirstColumns();
        $this->printFourthRowDaysTable();
    }
    
    private function printFourthRowsFirstColumns() : void {
        $this->writeCell(24.5, 12, 'Określenie', 1, 'C');
        $this->writeCell(24, 6, 'dnia miesiąca', 1, 'C');
        $x = $this->GetX();
        $y = $this->GetY();
        $this->Ln(6);
        $this->SetX($x - 24);
        $this->writeCell(24, 6, 'dnia tygodnia', 1, 'C');
        $this->Ln(6);
        $this->writeCell(48.5, 6, 'Kate. dnia pracy wolny-W pracujący-P', 1, 'C');
        $this->Ln(6);
        $this->writeCell(48.5, 6, 'Faktyczne godziny pracy', 1, 'C');
        $this->SetXY($x, $y);
    }
    
    private function printFourthRowDaysTable() : void {
        $columns = $this->getFourthHeaderDaysTableConfig();
        $data = $this->getFourthHeaderDaysTableData();
        $this->setMargin('left', 58.5);
        $x = $this->GetX() + (31 * 7);
        $y = $this->GetY();
        $cMargin = $this->cMargin;
        $this->cMargin = 0;
        $this->setCurrentSize(7);
        $this->makeTable($columns, $data, 0, 6);
        $this->setCurrentSize($this->font);
        $this->cMargin = $cMargin;
        $this->SetXY($x, $y);
        $this->writeCell(11.5, 12, 'suma', 1, 'C', true);
        $this->Ln(12);
        $this->SetX($x);
        $this->writeCell(11.5, 6, '', 1, 'C', true);
        $this->Ln(6);
        $this->SetX($x);
        $this->writeCell(11.5, 6, '', 1, 'C', true);
        $this->Ln(6);
        $this->setMargin('left', $this->leftMargin);
    }
    
    private function getFourthHeaderDaysTableConfig() : Columns {
        $lastDay = (int) date('t', strtotime($this->data->getYear() . '-' . $this->data->getMonth() . '-1'));
        $columns = new Columns();
        for($day = 1; $day <= 31; $day++){
            $date = new DateTime($this->data->getYear() . '-' . $this->data->getMonth() . '-' . $day);
            $filled = false;
            if($day <= $lastDay){
                $filled = $this->data->isDayOff($date);
            }
            $col = new Column(7, $day, $filled);
            $col->setAlignCenter();
            $columns->add($col);
        }
        return $columns;
    }
    
    private function getFourthHeaderDaysTableData() : Container {
        $data = new Container();
        $dayNumbers = $this->data->getRow('daysNumbers');
        $weekDayNames = $this->data->getRow('weekDays');
        $dayTypes = $this->data->getRow('dayTypes');
        $dayHours = $this->data->getRow('uniqueWorkHours');
        $data->add($dayNumbers);
        $data->add($weekDayNames);
        $data->add($dayTypes);
        $data->add($dayHours);
        return $data;
    }
    
    private function printTimePerDay() : void {
        $this->Ln(1);
        $this->SetX($this->leftMargin);
        $this->writeCell(48.5, 6, 'Rozkład czasu pracy w godzinach', 1, 'C');
        $this->printTimeForEachDay();
        $this->SetX(58.5);
        $x = $this->GetX() + (31 * 7);
        $this->SetX($x);
        $value = Time::msToClockNoSeconds($this->data->summarizeRow('standardWorkHours'));
        $this->writeCell(11.5, 6, $value, 1, 'C', true);
        $this->Ln(6);
    }
    
    private function printTimeForEachDay() : void {
        $lastDay = (int) date('t', strtotime($this->data->getYear() . '-' . $this->data->getMonth() . '-1'));
        $dayTimes = $this->data->getRow('standardWorkHours');
        for($day = 1; $day <= 31; $day++){
            if($day <= $lastDay){
                $date = new DateTime($this->data->getYear() . '-' . $this->data->getMonth() . '-' . $day);
                $text = Time::msToFullHours($dayTimes[$day]);
                $fill = $this->data->isDayOff($date);
                $this->writeCell(7, 6, $text, 1, 'C', $fill);
            }
            else{
                $this->writeCell(7, 6, '', 1, 'C');
            }
        }
    }
    
    private function printTimesTable() : void {
        $this->Ln(1);
        $x = $this->GetX() + 48.5;
        $y = $this->GetY();
        $this->printTimesTableFirstColumns();
        $this->SetXY($x, $y);
        $this->printTimesTableData();
    }
    
    private function printTimesTableFirstColumns() : void {
        $this->printTimesTableFirstColumnCell('Faktyczny czas pracy w urzędzie');
        $this->printTimesTableFirstColumnCell('Delegacja lub wyjście służbowe');
        $this->printAsMultiCell(48.5, 3, 'Praca w godzinach nadliczbowych lub poza normalnym czasem pracy na polecenie przełożonego');
        $this->Ln(9);
        $this->printTimesTableFirstColumnCell('WZN');
        $this->printTimesTableFirstColumnCell('Urlop wypoczynkowy');
        $this->printTripleMultiCell('Urlopy związane z rodzicielstwem', 'symbol', 'ilość godzin');
        $this->printTripleCell('Inne urlopy', 'symbol', 'ilość godzin');
        $this->printTimesTableFirstColumnCell('Choroba pracownika');
        $this->printTimesTableFirstColumnCell('Opieka nad członkiem rodziny');
        $this->printTimesTableFirstColumnCell('Opieka nad dzieckiem');
        $this->printTimesTableFirstColumnCell('Zwolnienia od pracy płatne');
        $this->printTripleMultiCell('Zwolnieni od pracy nie płatne', 'symbol', 'ilość godzin');
        $this->printTimesTableFirstColumnCell('Nieobecność nieusprawiedliwiona');
    }
    
    private function printTimesTableFirstColumnCell(string $text) : void {
        $this->writeCell(48.5, 5, $text, 1, 'C');
        $this->Ln(5);
    }
    
    private function printTripleMultiCell(string $first, string $second, string $third) : void {
        $cMargin = $this->cMargin;
        $this->cMargin = 0.2;
        $this->printAsMultiCell(24, 5, $first);
        $this->cMargin = $cMargin;
        $this->writeCell(24.5, 5, $second, 1, 'C');
        $this->Ln(5);
        $this->SetX(24 + $this->leftMargin);
        $this->writeCell(24.5, 5, $third, 1, 'C');
        $this->Ln(5);
    }
    
    private function printTripleCell(string $first, string $second, string $third) : void {
        $cMargin = $this->cMargin;
        $this->cMargin = 0.2;
        $this->writeCell(24, 10, $first, 1, 'C');
        $this->cMargin = $cMargin;
        $this->writeCell(24.5, 5, $second, 1, 'C');
        $this->Ln(5);
        $this->SetX(24 + $this->leftMargin);
        $this->writeCell(24.5, 5, $third, 1, 'C');
        $this->Ln(5);
    }
    
    private function printTimesTableData() : void {
        $columns = $this->generateConfigForTimesTableData();
        $data = $this->parseTimesTableData();
        $this->setMargin('left', 58.5);
        $cMargin = $this->cMargin;
        $this->cMargin = 0;
        $this->setCurrentSize(7);
        $y = $this->GetY();
        $firstDataSet = $this->getRange($data, 0, 1);
        $this->makeTable($columns, $firstDataSet, 0, 5);
        $secondDataSet = $this->getRange($data, 2, 2);
        $this->makeTable($columns, $secondDataSet, 0, 9);
        $thirdDataSet = $this->getRange($data, 3, 15);
        $this->makeTable($columns, $thirdDataSet, 0, 5);
        $this->setMargin('left', 58.5 + (31 * 7));
        $this->SetY($y);
        $this->printSumColumn();
        $this->setCurrentSize($this->font);
        $this->cMargin = $cMargin;
        $this->setMargin('left', $this->leftMargin);
    }
    
    private function generateConfigForTimesTableData() : Columns {
        $lastDay = (int) date('t', strtotime($this->data->getYear() . '-' . $this->data->getMonth() . '-1'));
        $columns = new Columns();
        for($day = 1; $day <= 31; $day++){
            $date = new DateTime($this->data->getYear() . '-' . $this->data->getMonth() . '-' . $day);
            $filled = false;
            if($day <= $lastDay){
                $filled = $this->data->isDayOff($date);
            }
            $col = new Column(7, $day, $filled);
            $col->setAlignCenter();
            $columns->add($col);
        }
        return $columns;
    }
    
    private function parseTimesTableData() : Container {
        $rows = array();
        for ($i = 0; $i < 16; $i++){
            $index = $this->convertTableIndexToDataIndex($i);
            if(is_numeric($index)){
                $rows[$i] = $this->parseTimesTableDataRow($index);
            }
            else{
                $rows[$i] = $this->data->getRow($index);
            }
        }
        return new Container($rows);
    }
    
    private function convertTableIndexToDataIndex(int $tableRowIndex) : string {
        $map = array(
            0 => 0, 
            1 => 1, 
            2 => 2, 
            3 => 3, 
            4 => 4, 
            5 => 's5', 
            6 => 5, 
            7 => 's6', 
            8 => 6,
            9 => 7, 
            10 => 8, 
            11 => 9, 
            12 => 10, 
            13 => 's11', 
            14 => 12, 
            15 => 13);
        return $map[$tableRowIndex];
    }
    
    private function parseTimesTableDataRow(int $index) : array {
        $row = $this->data->getRow($index);
        $parsed = $this->parseToDisplay($row);
        return $parsed;
    }
    
    private function printSumColumn() : void {
        $symbolRows = new Container(array(5,6,11));
        for($row = 0; $row < 13; $row++){
            if($symbolRows->contains($row)){
                $height = 10;
            }
            else{
                $height = 5;
            }
            if($row === 2){
                $height = 9;
            }
            $sum = $this->data->summarizeRow($row);
            $text = Time::msToClockNoSeconds($sum);
            $this->writeCell(11.5, $height, $text, 1, 'C', true);
            $this->Ln($height);
        }
    }
    
    private function printWatchtimeAndNightShift() : void {
        $this->Ln(1);
        $x = $this->GetX() + 48.5;
        $y = $this->GetY();
        $this->writeCell(48.5, 5, 'Dyżur', 1, 'C');
        $this->Ln(5);
        $this->writeCell(48.5, 5, 'Praca w porze nocnej', 1, 'C');
        $this->SetXY($x, $y);
        $this->setMargin('left', 58.5);
        $columns = $this->generateConfigForWatchtimeAndNightShift();
        $data = $this->parseWatchtimesAndNightShifts();
        $cMargin = $this->cMargin;
        $this->cMargin = 0;
        $this->setCurrentSize(7);
        $this->makeTable($columns, $data);
        $this->setCurrentSize($this->font);
        $this->cMargin = $cMargin;
        $this->setMargin('left', $this->leftMargin);
        $this->SetX($this->leftMargin);
    }
    
    private function generateConfigForWatchtimeAndNightShift() : Columns {
        $lastDay = (int) date('t', strtotime($this->data->getYear() . '-' . $this->data->getMonth() . '-1'));
        $columns = new Columns();
        for($day = 1; $day <= 31; $day++){
            $date = new DateTime($this->data->getYear() . '-' . $this->data->getMonth() . '-' . $day);
            $filled = false;
            if($day <= $lastDay){
                $filled = $this->data->isDayOff($date);
            }
            $col = new Column(7, $day, $filled);
            $col->setAlignCenter();
            $columns->add($col);
        }
        $col = new Column(11.5, 'sum', true);
        $col->setAlignCenter();
        $columns->add($col);
        return $columns;
    }
    
    private function parseWatchtimesAndNightShifts() : Container {
        $watchtimes = $this->data->getRow(13);
        $watchtimesParsed = $this->parseToDisplay($watchtimes);
        $nightShifts = $this->data->getRow(14);
        $nightShiftsParsed = $this->parseToDisplay($nightShifts);
        $watchtimesParsed['sum'] = Time::msToClockNoSeconds($this->data->summarizeRow(13));
        $nightShiftsParsed['sum'] = Time::msToClockNoSeconds($this->data->summarizeRow(14));
        $result = new Container();
        $result->add($watchtimesParsed);
        $result->add($nightShiftsParsed);
        return $result;
    }
    
    private function printSummary() : void {
        $this->printSummaryPartOne();
        $this->printSummaryPartTwo();
    }
    
    private function printSummaryPartOne() : void {
        $startY = $this->GetY();
        $this->writeCell(0, 5, 'Bilans czasu Pracy');
        $this->Ln(5);
        $this->writeCell(111, 5, 'Norma czasu pracy w bieżącym okresie rozliczeniowmym', 1);
        $standardWorkTime = $this->data->summarizeRow('standardWorkHours');
        $this->writeCell(32.5, 5, Time::msToClockNoSeconds($standardWorkTime), 1, 'C');
        $hoursSets = $this->data->getUniqueHoursSets();
        $width = 133.5 / 3;
        $height = ($hoursSets->length() > 12)? 3 : 5;
        $x = $this->GetX();
        $count = 1;
        foreach ($hoursSets->toArray() as $roman => $set) {
            $this->writeCell($width, $height, $roman . ' - od ' . $set['start'] . ' do ' . $set['end'], 0, 'C');
            if($count % 3 === 0){
                $this->SetXY($x, $this->GetY() + $height);
            }
            $count++;
        }
        $this->SetXY($this->leftMargin, $startY + 10);
    }
    
    private function printSummaryPartTwo() : void {
        $this->writeCell(111, 5, 'Czas przepracowany w bieżącym okresie rozliczeniowym', 1);
        $worktime = $this->data->getCurrentWorktime();
        $this->writeCell(32.5, 5, Time::msToClockNoSeconds($worktime), 1, 'C');
        $this->Ln(5);
        $this->writeCell(111, 5, 'Liczba godzin przeniesiona z poprzedniego okresu rozliczeniowego', 1);
        $previousOvertime = $this->data->getPreviousOvertime();
        $this->writeCell(32.5, 5, Time::msToClockNoSeconds($previousOvertime), 1, 'C');
        $this->Ln(5);
        $passingOvertime = $this->data->getPassingOvertime();
        $this->writeCell(111, 5, 'Liczba godzin do przeniesienia na następny okres rozliczeniowy', 1);
        $this->writeCell(32.5, 5, Time::msToClockNoSeconds($passingOvertime), 1, 'C');
        $this->Ln(5);
    }
    
    private function printFooter() {
        $this->setMargin('bottom', 5);
        $this->Ln(1);
        $x = $this->GetX();
        $this->writeCell(69.6, 15, '', 1);
        $this->writeCell(63, 15, '', 1);
        $this->writeCell(56, 15, '', 1);
        $this->writeCell(89, 15, '', 1);
        $this->SetX($x);
        $this->writeCell(69.6, 4, 'Sporządził: (podpis, data)', 0, 'C');
        $this->writeCell(63, 4, 'Zatwierdził: (pieczątka i podpis, data)', 0, 'C');
        $this->writeCell(56, 4, 'Podpis pracownika, data', 0, 'C');
        $this->writeCell(89, 4, 'Uwagi:');
    }
    
    private function getRange(Container $data, int $start, int $end) : Container {
        $result = new Container();
        foreach ($data->toArray() as $index => $item) {
            if($index >= $start && $index <= $end){
                $result->add($item);
            }
        }
        return $result;
    }
    
    private function printAsMultiCell(float $width, float $cellRowHeight, string $text, int $border = 1, string $align = 'C') : void {
        $x = $this->GetX() + $width;
        $y = $this->GetY();
        $this->writeMulticell($width, $cellRowHeight, $text, $border, $align);
        $this->SetXY($x, $y);
    }
    
    private function parseToDisplay(array $row) : array {
        $result = array();
        foreach ($row as $key => $time){
            if($time > 0){
                $result[$key] = Time::msToClockNoSeconds($time);
            }
            else{
                $result[$key] = '';
            }
        }
        return $result;
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

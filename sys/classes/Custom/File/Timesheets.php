<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Custom\File\Tools\Timesheets\TimesheetsData as TimesheetsData;
use Custom\File\Tools\Timesheets\DayDetails as DayDetails;
use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Config\Template as Template;
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
class Timesheets{
    private int $month;
    private int $year;
    private string $title;
    private TimesheetsData $data;
    private ExcelEditor $editor;
    
    private function __construct(int $month, int $year, string $username) {
        $this->month = $month;
        $this->year = $year;
        $this->data = new TimesheetsData($month, $year, $username);
        $this->title = 'Ewidencja ';
        $path = Template::getLocalPath('timesheets.xlsx');
        $this->editor = new ExcelEditor();
        $this->editor->openFile($path);
        $this->title = $this->data->getFullUsername() . '_' . $this->data->getMonth() . '_' . $this->data->getYear();
    }
    
    public static function generate(int $month, int $year, string $username) : void {
        $timesheets = new Timesheets($month, $year, $username);
        $timesheets->print();
        $filename = $timesheets->getTitle();
        $timesheets->send($filename);
    }
    
    protected function getTitle() : string {
        return $this->title;
    }
    
    protected function print() : void {
        $this->writeHeader();
        $this->writeData();
    }
    
    private function writeHeader() : void {
        $sheet = $this->editor->getCurrentSheetName();
        $this->editor->writeToCell($sheet, 'K2', $this->data->getFullUsername());
        $this->editor->writeToCell($sheet, 'C5', $this->data->getYear());
        $this->editor->writeToCell($sheet, 'B9', $this->data->getMonth());
        $this->editor->writeToCell($sheet, 'G7', $this->data->getWorkdaysCount());
        $dailyJobTime = Time::msToFullHours($this->data->getUserDailyJobTimeInMins() * 60 * 1000);
        $this->editor->writeToCell($sheet, 'K6', $dailyJobTime);
        $previousOvertime = $this->toExcelTime($this->data->getPreviousOvertimeInMins());
        $this->editor->writeToCell($sheet, 'P6', $previousOvertime);
        $this->editor->writeToCell($sheet, 'Q6', $previousOvertime);
    }
    
    private function writeData() : void {
        $sheet = $this->editor->getCurrentSheetName();
        for($day = 1; $day <= 31; $day++){
            $date = new DateTime($this->year . '-' . $this->month . '-' . $day);
            $this->writeDataCol($date, $sheet);
        }
    }
    
    private function writeDataCol(DateTime $date, string $sheet) : void {
        $details = $this->data->getDayDetails($date);
        $day = (int) $date->format('d');
        $this->writeDateDetails($sheet, $details, $day);
        $this->writeWorktimeDetails($sheet, $details, $day);
    }
    
    private function writeDateDetails(string $sheet, DayDetails $details, int $day) : void {
        $weekdayCell = $this->editor->getAddress(11, $day + 2);
        $this->editor->writeToCell($sheet, $weekdayCell, $details->getWeekdayText());
        $letterCell = $this->editor->getAddress(12, $day + 2);
        $this->editor->writeToCell($sheet, $letterCell, $details->getDayLetter());
        if($details->getStandardWorkdayTime() !== 0 && $details->getDayLetter() === 'P'){
            $worktimeCell = $this->editor->getAddress(15, $day + 2);
            $dayWorktime = $this->toExcelTimeInMs($details->getStandardWorkdayTime());
            $this->editor->writeToCell($sheet, $worktimeCell, $dayWorktime);
        }
        if($details->getWorkdayStart() !== '' && $details->getWorkdayEnd() !== '' && $details->getDayLetter() === 'P'){
            $startCell = $this->editor->getAddress(17, $day + 2);
            $this->editor->writeToCell($sheet, $startCell, $details->getWorkdayStart());
            $endCell = $this->editor->getAddress(18, $day + 2);
            $this->editor->writeToCell($sheet, $endCell, $details->getWorkdayEnd());
        }
    }
    
    private function writeWorktimeDetails(string $sheet, DayDetails $details, int $day) : void {
        $this->writeTimeToCell($sheet, 22, $day + 2, $details->getOfficeTime());
        $this->writeTimeToCell($sheet, 23, $day + 2, $details->getDelegationTime());
        $this->writeTimeToCell($sheet, 24, $day + 2, $details->getOvertime());
        $this->writeTimeToCell($sheet, 25, $day + 2, $details->getWznTime());
        $this->writeTimeToCell($sheet, 26, $day + 2, $details->getNightShiftTime());
        $this->writeTextToCell($sheet, 27, $day + 2, $details->getAbsenceSymbol());
        $this->writeTimeToCell($sheet, 28, $day + 2, $details->getAbsenceTime());
        $this->writeTextToCell($sheet, 29, $day + 2, $details->getWorkOffSymbol());
        $this->writeTimeToCell($sheet, 30, $day + 2, $details->getWorkOffTime());
    }
    
    private function writeTimeToCell(string $sheet, int $row, int $col, int $value) : void {
        if($value > 0){
            $cell = $this->editor->getAddress($row, $col);
            $time = $this->toExcelTimeInMs($value);
            $this->editor->writeToCell($sheet, $cell, $time);
        }
    }
    
    private function writeTextToCell(string $sheet, int $row, int $col, string $text) : void {
        if($text !== ''){
            $cell = $this->editor->getAddress($row, $col);
            $this->editor->writeToCell($sheet, $cell, $text);
        }
    }
    
    public function send(string $filename) : void {
        $this->editor->sendToBrowser($filename);
    }
    
    private function toExcelTime(int $time) : string {
        $remaining = ($time -   floor($time / 60) * 60);
        if($remaining < 10){
            $remaining = '0' . $remaining;
        }
        $hours = floor($time / 60);
        return '=('.$hours.'/24+TIME(0,'.$remaining.',0))';
    }
    
    private function toExcelTimeInMs(int $time) : string {
        $mins = floor($time / 1000 / 60);
        $remaining = ($mins -   floor($mins / 60) * 60);
        if($remaining < 10){
            $remaining = '0' . $remaining;
        }
        $hours = floor($mins / 60);
        return '=('.$hours.'/24+TIME(0,'.$remaining.',0))';
    }
}

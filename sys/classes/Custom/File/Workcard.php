<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Data\Access\Views\UsersWithoutPasswordsView as UsersWithoutPasswordsView;
use Custom\File\Tools\Workcard\Rows as Rows;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\INI\AppConfig as AppConfig;

/**
 * Description of Workcard
 *
 * @author Tanzar
 */
class Workcard extends PDFMaker{
    private string $title;
    private int $month;
    private string $monthText;
    private int $year;
    private string $username;
    private string $fullUserName;
    private int $margin;
    private int $bottomMargin;
    private int $font;
    private Container $config;
    private Rows $rows;
    
    private function __construct(int $month, int $year, string $username) {
        parent::__construct('P', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->font = 10;
        $this->setCurrentSize($this->font);
        $this->margin = 10;
        $this->bottomMargin = 24;
        $this->setMargin('all', $this->margin);
        $this->setMargin('bottom', $this->bottomMargin);
        $this->month = $month;
        $this->year = $year;
        $this->username = $username;
        $this->loadUser();
        $this->rows = new Rows($month, $year, $username);
        $appconfig = AppConfig::getInstance();
        $this->config = $appconfig->getAppConfig();
        $this->SetAuthor($this->config->get('name') . ' web app');
        $languages = Languages::getInstance();
        $months = $languages->get('months');
        $this->monthText = $months[$month];
        $this->title = 'Karta pracy za ' . $this->monthText . ' ' . 
                $year . ' dla ' . $this->fullUserName;
        $this->SetTitle($this->title, true);
    }
    
    private function loadUser() : void {
        $view = new UsersWithoutPasswordsView();
        $user = $view->getByUsername($this->username);
        $this->fullUserName = $user->get('name') . ' ' . $user->get('surname');
    }
    
    public static function generate(int $month, int $year, string $username) : void {
        $pdf = new Workcard($month, $year, $username);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    private function print() : void {
        $this->printTop();
        $this->printTableHeaders();
        $this->printTable();
        $this->printTableFoot();
        $this->printFoot();
    }
    
    private function printTop() : void {
        $first = $this->config->get('organization_full');
        $this->writeCell(0, 5, $first);
        $this->Ln(5);
        $second = 'KARTA PRACY PRACOWNIKA INSPEKCYJNO-TECHNICZNEGO';
        $this->writeCell(0, 5, $second, 0, 'C');
        $this->Ln(5);
        $name = 'Imię Nazwisko: ' . $this->fullUserName;
        $month = 'Miesiąc: ' . $this->monthText;
        $year = 'Rok: ' . $this->year;
        $text = $name . '     ' . $year . '     ' . $month;
        $this->writeCell(0, 5, $text);
        $this->Ln(7);
    }
    
    private function printTableHeaders() : void {
        $this->writeCell(9, 7, 'Data', 1, 'C');
        $this->writeCell(50, 7, 'Miejsce wykonywanej pracy', 1, 'C');
        $this->writeCell(40, 7, 'Rodzaj Czynności ', 1, 'C');
        $this->writeCell(70, 7, 'Nr dokumentu', 1, 'C');
        $this->writeCell(23, 7, 'Uwagi', 1, 'C');
        $this->Ln(7);
    }
    
    private function printTable() : void {
        $columns = $this->generateTableConfig();
        $data = $this->rows->getRows();
        $rowsToFill = new Container();
        foreach ($data->toArray() as $index => $item) {
            $row = new Container($item);
            if($row->get('fill')){
                $rowsToFill->add($index);
            }
        }
        $height = $this->calcRowsHeight();
        $this->makeTable($columns, $data, 0, $height, $rowsToFill);
    }
    
    private function calcRowsHeight() : int {
        $min = 5;
        $max = 7;
        $limit = $max * 31;
        $rows = $this->rows->getRows();
        $rowCount = $rows->length();
        $value = $max;
        while($value > $min && $value * $rowCount > $limit){
            $value--;
        }
        if($value * $rowCount > $limit){
            $value = $max;
        }
        return $value;
    }
    
    private function generateTableConfig() : Columns {
        $columns = new Columns();
        $day = new Column(9, 'day');
        $columns->add($day);
        $location = new Column(50, 'location');
        $columns->add($location);
        $activitiy = new Column(40, 'activity');
        $columns->add($activitiy);
        $document = new Column(70, 'document');
        $columns->add($document);
        $hours = new Column(23, 'hours');
        $columns->add($hours);
        return $columns;
    }
    
    private function printTableFoot() : void {
        $this->Ln(5);
        $this->writeCell(0, 5, 'Ilość dni nadzorczych/kontrolnych: ' . $this->rows->countTotalInspections());
        $this->Ln(5);
        $this->writeCell(44.5, 5, 'w tym: 1) podziemnych');
        $this->writeCell(0, 5, $this->rows->countUndergroundInspections());
        $this->Ln(5);
        $this->SetX(21);
        $this->writeCell(0, 5, '2) powierzchniowych ' . $this->rows->countSurfaceInspections());
    }
    
    private function printFoot() : void {
        $this->setMargin('bottom', 0);
        $this->SetY($this->h - $this->bottomMargin);
        $halfWidth = round(($this->w - ($this->margin * 2)) / 2);
        $this->writeCell($halfWidth, 5, 'Potwierdzenie dyrektora urzędu', 0, 'C');
        $this->writeCell($halfWidth, 5, 'Podpis pracownika', 0, 'C');
        $this->Ln(10);
        $this->writeCell($halfWidth, 5, '................................................................', 0, 'C');
        $this->writeCell($halfWidth, 5, '................................................................', 0, 'C');
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

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File;

use Data\Access\Views\EquipmentDetailsView as EquipmentDetailsView;
use Data\Access\Views\InstrumentUsageDetailsView as InstrumentUsageDetailsView;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;

/**
 * Description of InstrumentUsageCard
 *
 * @author Tanzar
 */
class InstrumentUsageCard extends PDFMaker{
    private string $title;
    private int $instrumentId;
    private int $year;
    private int $margin;
    private int $font;
    private Container $config;
    private Container $rows;
    private Container $instrument;
    
    
    private function __construct(int $instrumentId, int $year) {
        parent::__construct('P', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->font = 12;
        $this->setCurrentSize($this->font);
        $this->margin = 10;
        $this->setMargin('all', $this->margin);
        $this->SetAuthor('Scheduler web app');
        $this->instrumentId = $instrumentId;
        $this->year = $year;
        $appconfig = AppConfig::getInstance();
        $this->config = $appconfig->getAppConfig();
        $this->title = 'Karta ewidencji wykorzystania przyrządu';
        $this->SetTitle($this->title, true);
        $this->loadInstrument();
        $this->loadUsages();
    }
    
    private function loadInstrument() : void {
        $view = new EquipmentDetailsView();
        $this->instrument = $view->getById($this->instrumentId);
    }
    
    private function loadUsages() : void {
        $view = new InstrumentUsageDetailsView();
        $usages = $view->getAllByEquipmentIdAndYear($this->instrumentId, $this->year);
        $this->rows = new Container();
        $count = 1;
        foreach ($usages->toArray() as $item) {
            $usage = new Container($item);
            $row = $this->parseRow($count, $usage);
            $this->rows->add($row);
            $count++;
        }
    }
    
    private function parseRow(int $count, Container $usage) : array {
        $row = array(
            'rowNumber' => $count,
            'date' => $usage->get('date'),
            'location' => $usage->get('location'),
            'document' => $usage->get('document_number'),
            'user' => $usage->get('document_assigned_name') . ' ' . $usage->get('document_assigned_surname'),
            'remarks' => $usage->get('remarks')
        );
        if($usage->get('recommendation_decision')){
            $row['recommendationDecision'] = 'TAK';
        }
        else{
            $row['recommendationDecision'] = 'NIE';
        }
        return $row;
    }
    
    public static function generate(int $instrumentId, int $year) : void {
        $pdf = new InstrumentUsageCard($instrumentId, $year);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    private function print() : void {
        $this->printHeadText();
        $this->printInstrumentDetails();
        $this->printTableHeaders();
        $this->printUsagesTable();
    }
    
    private function printHeadText() : void {
        $this->Ln(1);
        $this->writeCell(0, 5, 'Karta ewidencyjna użycia przyrządu kontrolno-pomiarowego', 0, 'C');
        $this->Ln(6);
    }
    
    private function printInstrumentDetails() : void {
        $this->printMultiTextCell(0, 'Nazwa Urzędu Górniczego / Komórki organizacyjnej WUG:', $this->config->get('organization_full'));
        $this->printMultiTextCell(0, 'Nazwa przyrządu kontrolno-pomiarowego:', $this->instrument->get('name'));
        $y = $this->GetY();
        $this->printMultiTextCell(120, 'Nr ewidencyjny przyrządu kontrolno-pomiarowego:', $this->instrument->get('inventory_number'));
        $this->SetXY(120 + $this->margin, $y);
        $this->printMultiTextCell(70, 'Rok rozpoczęcia użytkowania przyrządu:', $this->instrument->get('start_date'));
        $this->setCurrentSize(8);
        $this->writeCell(60, 7, 'Rok ' . $this->year, 1, 'C');
        $this->writeCell(100, 7, 'Miesiąc', 1, 'C');
        $this->writeCell(30, 7, 'Nr Karty 1', 1, 'C');
        $this->Ln(7);
    }
    
    private function printMultiTextCell(int $width, string $topText, string $bottomText) : void {
        $x = $this->getX();
        $this->writeCell($width, 14, '', 1);
        $this->setCurrentSize(8);
        $this->SetX($x);
        $this->writeCell($width, 7, $topText);
        $this->Ln(7);
        $this->SetX($x);
        $this->setCurrentSize(12);
        $this->writeCell($width, 7, $bottomText);
        $this->Ln(7);
        $this->setCurrentSize($this->font);
    }
    
    private function printTableHeaders() : void {
        $this->printTableHeadersPartOne();
        $this->printTableHeadersPartTwo();
    }
    
    private function printTableHeadersPartOne() : void {
        $this->writeCell(10, 35, 'Lp', 1, 'C');
        $x = $this->GetX();
        $y = $this->GetY();
        $this->writeCell(20, 35, '', 1, 'C');
        $this->SetXY($x, $y + 10);
        $this->writeMultiCell(20, 5, 'Data pomiaru lub badania', 0, 'C');
        $x = $x + 20;
        $this->SetXY($x, $y);
        $this->writeCell(44, 35, '', 1, 'C');
        $this->SetXY($x + 7, $y + 10);
        $this->writeMultiCell(30, 5, 'Nazwa ZG / miejsce wykonania pomiaru lub badania', 0, 'C');
        $x = $x + 44;
        $this->SetXY($x, $y);
        $this->writeCell(43, 35, '', 1, 'C');
        $this->SetXY($x + 5, $y + 10);
        $this->writeMultiCell(33, 5, 'Nr protokołu lub notatki służbowej pomiar lub badanie', 0, 'C');
        $x = $x + 43;
        $this->SetXY($x, $y);
    }
    
    private function printTableHeadersPartTwo() : void {
        $x = $this->GetX();
        $y = $this->GetY();
        $this->writeCell(32, 35, '', 1, 'C');
        $this->SetXY($x + 1.5, $y + 10);
        $this->writeMultiCell(29, 5, 'Imię i nazwisko osoby wykonującej pomiar lub badanie', 0, 'C');
        $x = $x + 32;
        $this->SetXY($x, $y);
        $this->writeCell(18, 35, '', 1, 'C');
        $this->SetXY($x, $y);
        $cellMargin = $this->cMargin;
        $this->cMargin = 0;
        $this->writeMultiCell(18, 5, 'Czy wydano decyzję lub zalecenie na podstawie pomiaru lub badania (TAK/NIE)', 0, 'C');
        $x = $x + 18;
        $this->SetXY($x, $y);
        $this->writeCell(23, 35, '', 1, 'C');
        $this->SetXY($x, $y + 5);
        $this->writeMultiCell(23, 5, 'Inne uwagi dotyczące stwierdzonej', 0, 'C');
        $this->SetXY($x, $y + 20);
        $this->writeCell(23, 5, 'nieprawidłowości', 0 , 'C');
        $this->cMargin = $cellMargin;
        $this->Ln(15);
    }
    
    private function printUsagesTable() : void {
        $columns = $this->generateConfigForTable();
        $this->makeTable($columns, $this->rows);
    }
    
    private function generateConfigForTable() : Columns {
        $columns = new Columns();
        $rowNumber = new Column(10, 'rowNumber');
        $columns->add($rowNumber);
        $date = new Column(20, 'date');
        $columns->add($date);
        $location = new Column(44, 'location');
        $columns->add($location);
        $document = new Column(43, 'document');
        $columns->add($document);
        $user = new Column(32, 'user');
        $columns->add($user);
        $recommendationDecision = new Column(18, 'recommendationDecision');
        $columns->add($recommendationDecision);
        $remarks = new Column(23, 'remarks');
        $columns->add($remarks);
        return $columns;
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

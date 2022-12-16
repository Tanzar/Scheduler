<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\File\PDFMaker\Columns as Columns;
use Custom\Statistics\Options\Group as Group;
use Custom\Statistics\Exceptions\UnsupportedStatisticsTypeException as UnsupportedStatisticsTypeException;
/**
 * Description of StatsPDF
 *
 * @author Tanzar
 */
class StatsPDF extends PDFMaker{
    private string $title;
    private int $margins = 5;
    private int $colMinWidth = 12;
    private int $colMaxWidth = 40;
    private Container $stats;
    
    
    private function __construct(Container $stats) {
        $this->typeCheck($stats);
        $this->stats = $stats;
        $this->title = $stats->get('title');
        parent::__construct('L', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->setMargin('all', $this->margins);
        $this->SetY($this->margins);
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $this->SetAuthor($cfg->get('name') . ' web app');
        $this->SetTitle($this->title, true);
    }
    
    private function typeCheck(Container $stats) : void {
        if($stats->isValueSet('type')){
            if($stats->get('type') !== 'table' && $stats->get('type') !== 'multiple_tables'){
                throw new UnsupportedStatisticsTypeException('only tables can be put into PDF');
            }
        }
        else{
            throw new UnsupportedStatisticsTypeException('result does not heave type set');
        }
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    public static function formFile(Container $stats) : void {
        $pdf = new StatsPDF($stats);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function print() : void {
        if($this->stats->get('type') === 'table'){
            $this->printSingleTable($this->stats);
        }
        elseif ($this->stats->get('type') === 'multiple_tables'){
            $this->SetFontSize(14);
            $this->writeMulticell(0, 7, $this->stats->get('title'), 0, 'C');
            $this->Ln(3);
            $tables = $this->stats->get('data');
            foreach ($tables as $key => $table) {
                $data = new Container($table);
                $this->printSingleTable($data);
                if($key + 1 < count($tables)){
                    $this->AddPage();
                }
            }
        }
    }
    
    private function printSingleTable(Container $data, int $titleHeight = 7, int $titleSize = 14) : void {
        $this->SetFontSize($titleSize);
        $this->writeMulticell(0, $titleHeight, $data->get('title'), 0, 'C');
        $this->Ln(3);
        $this->SetFontSize(10);
        $cells = $data->get('cells');
        $this->SetFontSize(8);
        $this->printColsFromX($cells);
    }
    
    private function printColsFromX(array $cells, int $x = 1) : void {
        $headers = $cells[0];
        $columns = $this->firstColumns($cells);
        $totalWidth = $columns->totalWidth();
        if($columns->length() > 1){
            $x = $columns->length();
        }
        $space = $this->w - ( 2 * $this->margins);
        while($totalWidth < $space && $x < count($headers)){
            $width = $this->calcColumnWidth($cells, $x);
            if($width + $totalWidth <= $space){
                $fill = $this->fillCol($headers, $x);
                $col = new Column($width, $x, $fill);
                $columns->add($col);
            }
            $totalWidth += $width;
            $x++;
        }
        $this->makeTable($columns, new Container($cells), 0, 5, new Container([0]));
        if($x < count($headers)){
            $this->printColsFromX($cells, $x);
        }
    }
    
    private function firstColumns(array $cells) : Columns {
        $columns = new Columns();
        $width = $this->calcColumnWidth($cells, 0);
        $columns->add(new Column($width, 0, true));
        if($cells[0][1] === Group::Users->value){
            $width = $this->calcColumnWidth($cells, 1);
            $columns->add(new Column($width, 1, true));
        }
        return $columns;
    }
    
    private function calcColumnWidth(array $cells, int $x) : float {
        $width = 0;
        for($y = 0; $y < count($cells); $y++){
            $width = max($width, $this->calcWidth($cells[$y][$x]));
        }
        if($width < $this->colMinWidth){
            $width = $this->colMinWidth;
        }
        return $width;
    }
    
    private function calcWidth(string $text) : float {
        $width = $this->GetStringWidth($text) + 2;
        if($width > $this->colMaxWidth){
            $width = $this->colMaxWidth;
        }
        elseif($width < $this->colMinWidth){
            $width = $this->colMinWidth;
        }
        return $width;
    }
    
    private function fillCol(array $headers, int $x): bool {
        return ($x === 0  || ($x === 1 && $headers[$x] === Group::Users->value));
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

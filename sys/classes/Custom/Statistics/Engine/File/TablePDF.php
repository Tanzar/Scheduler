<?php

/*
 * This code is free to use, just remember to give credit.
 */


namespace Custom\Statistics\Engine\File;

use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\File\PDFMaker\PDFMaker as PDFMaker;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\File\PDFMaker\Columns as Columns;
/**
 * Description of TablePDF
 *
 * @author Tanzar
 */
class TablePDF extends PDFMaker {
    private Container $data;
    private int $margins = 5;
    private int $colMinWidth = 12;
    private int $colMaxWidth = 40;
    private string $title;
    
    private function __construct(Container $data) {
        $this->data = $data;
        parent::__construct('L', 'A4');
        $this->SetFillColor(200, 200, 200);
        $this->setMargin('all', $this->margins);
        $this->SetY($this->margins);
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $this->SetAuthor($cfg->get('name') . ' web app');
        $this->title = $data->get('title');
        $this->SetTitle($this->title, true);
    }
    
    public function getTitle() : string {
        return $this->title;
    }
    
    public static function generate(Container $data) : void {
        $pdf = new TablePDF($data);
        $pdf->print();
        $filename = $pdf->getTitle();
        $pdf->send($filename);
    }
    
    public function print(int $titleHeight = 7, int $titleSize = 14) : void {
        $data = $this->data;
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
            $this->AddPage();
            $this->printColsFromX($cells, $x);
        }
    }
    
    private function firstColumns(array $cells) : Columns {
        $columns = new Columns();
        $width = $this->calcColumnWidth($cells, 0);
        $columns->add(new Column($width, 0, true));
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
        return ($x === 0);
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

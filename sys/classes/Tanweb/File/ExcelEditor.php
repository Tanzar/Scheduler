<?php

namespace Tanweb\File;

/*
 * This code is free to use, just remember to give credit.
 */

/**
 * Adapter to excel library (PhpSpreadsheet), 
 * use to manage xlsx files, create and edit templates
 *
 * @author Grzegorz Spakowski, Tanzar
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Xlsx;
use \PhpOffice\PhpSpreadsheet\IOFactory as IOFactory;

class ExcelEditor {
    private $name;
    private Spreadsheet $spreadsheet;
    
    public function openFile($path){
        $this->name = $path;
        $this->spreadsheet = IOFactory::load($path);
    }
    
    public function newFile($name, string $sheetName = 'Worksheet'){
        $this->name = $name;
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getActiveSheet()->setTitle($sheetName);
    }
    
    public function addSheet($name){
        $this->spreadsheet->createSheet();
        $count = $this->spreadsheet->getSheetCount();
        $this->spreadsheet->setActiveSheetIndex($count - 1);
        $this->spreadsheet->getActiveSheet()->setTitle($name);
    }
    
    public function writeToCell($sheetName, $cell, $value){
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->setCellValue($cell, $value);
    }
    
    public function saveLocally($path){
        $this->spreadsheet->save($path);
    }
    
    public function sendToBrowser($filename){
        $writer = new Xlsx($this->spreadsheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        $writer->save("php://output");
    }
}

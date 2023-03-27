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
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as Reader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Writer;
use PhpOffice\PhpSpreadsheet\Style\Border as Border;
use PhpOffice\PhpSpreadsheet\Style\Color as Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill as Fill;
use ZipArchive;

class ExcelEditor {
    private $name;
    private Spreadsheet $spreadsheet;
    
    public function openFile(string $path) : void {
        $this->name = $path;
        $reader = new Reader();
        $this->spreadsheet = $reader->load($path);
    }
    
    public function newFile(string $name, string $author, string $sheetName = 'Worksheet') : void {
        $this->name = $name;
        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getActiveSheet()->setTitle($sheetName);
        $this->spreadsheet->getProperties()->setCreator($author);
        $this->spreadsheet->getProperties()->setLastModifiedBy($author);
    }
    
    public function addSheet(string $name) : void {
        $this->spreadsheet->createSheet();
        $count = $this->spreadsheet->getSheetCount();
        $this->spreadsheet->setActiveSheetIndex($count - 1);
        $this->spreadsheet->getActiveSheet()->setTitle($name);
    }
    
    public function writeToCell(string $sheetName, string $cell, string $value) : void {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->setCellValue($cell, $value);
    }
    
    public function mergeCells(string $sheetName, string $start, string $end) : void {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->mergeCells($start . ':' . $end);
    }
    
    public function setBorder(string $sheetName, string $cell) : void {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->getStyle($cell)->getBorders()->getOutline()
                ->setBorderStyle(Border::BORDER_THIN)->setColor(new Color(Color::COLOR_BLACK));
    }
    
    public function fillCell(string $sheetName, string $cell, string $colorCode) : void {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($colorCode);
    }
    
    public function centerCells(string $sheetName, string $cell) : void {
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
    }
    
    public function setColumnAutosize(string $sheetName, int $col) : void {
        $letter = $this->getColFromNumber($col);
        $sheet = $this->spreadsheet->getSheetByName($sheetName);
        $sheet->getColumnDimension($letter)->setAutoSize(true);
    }
    
    public function setMargins(float $top, float $bottom, float $left, float $right) : void {
        $sheet = $this->spreadsheet->getActiveSheet();
        $margins = $sheet->getPageMargins();
        $margins->setTop($top);
        $margins->setBottom($bottom);
        $margins->setLeft($left);
        $margins->setRight($right);
    }
    
    public function saveLocally(string $path) : void {
        $this->spreadsheet->save($path);
    }
    
    public function sendToBrowser(string $filename) : void {
        $this->spreadsheet->getProperties()->setTitle($filename);
        $writer = new Writer($this->spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save("php://output");
    }
    
    public function getCurrentSheetName() : string {
        $names = $this->spreadsheet->getSheetNames();
        $index = $this->spreadsheet->getActiveSheetIndex();
        return $names[$index];
    }
    
    public function getAddress(int $row, int $col) : string {
        return $this->getColFromNumber($col) . $row;
    }
    
    private function getColFromNumber(int $num) : string {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return $this->getColFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
}

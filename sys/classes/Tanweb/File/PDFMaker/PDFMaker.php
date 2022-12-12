<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\File\PDFMaker;

use Tanweb\File\PDFMaker\FPDFEdited as FPDF;
use Tanweb\File\PDFMaker\Columns as Columns;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\Utility as Utility;
use Tanweb\Container as Container;

/**
 * Adapter to FPDF, use to create pdf files
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class PDFMaker extends FPDF{
    private $font = 'arialpl';
    private $size = 9;
    private $style = '';
    
    public function __construct($orientation, $size){
        parent::__construct($orientation, 'mm', $size);
        $this->AddFont('arialpl','','arialpl.php', true); 
        $this->addPage($orientation);
        $this->setFont($this->font, $this->style, $this->size);
        $this->AliasNbPages();
    }
    
    public function setCurrentFont($font){
        if(isset($font) && Utility::isString($font)){
            $this->font = $font;
        }
        $this->setFont($this->font, $this->style, $this->size);
    }
    
    public function setCurrentSize($size){
        if(isset($size) && Utility::isNumeric($size)){
            $this->size = $size;
        }
        $this->setFont($this->font, $this->style, $this->size);
    }
    
    public function getCurrentFontSize() : int {
        return $this->size;
    }
    
    //--- setCurrentStyle ---//
    public function setCurrentStyle($style){
        if($this->isAcceptableStyle($style)){
            $this->style = $style;
        }
        $this->setFont($this->font, $this->style, $this->size);
    }
    /*
     * Accepts only B, I, U chars aand empty string
     */
    private function isAcceptableStyle($style){
        if($style === ''){
            return true;
        }
        $lower = Utility::toLowerCase($style);
        $chars = Utility::toCharArray($lower);
        if(Utility::count($chars) <= 3){
            foreach ($chars as $char){
                if(!Utility::inArray(["B", "I", "U"], $char)){
                    return false;
                }
            }
            return true;
        }
        else{
            return false;
        }
    }
    
    //--- setCurrentStyle ---//
    
    public function setMargin($margin, $size){
        switch($margin){
            case 'top':
                $this->SetTopMargin($size);
                break;
            case 'left':
                $this->SetLeftMargin($size);
                break;
            case 'right':
                $this->SetRightMargin($size);
                break;
            case 'bottom':
                $this->SetAutoPageBreak(true, $size);
                break;
            case 'all':
                $this->SetMargins($size, $size, $size);
                $this->SetAutoPageBreak(true, $size);
                break;
        }
    }
    
    public function writeMulticell($width, $height, $text, $border = 0, $align = 'L', $fill = false){
        if($height === 0){
            $height = $this->size;
        }
        $this->MultiCell($width, $height, $text, $border, $align, $fill);
    }
    
    public function writeCell($width, $height, $text, $border = 0, $align = 'L', $fill = false){
        if($height === 0){
            $height = $this->size;
        }
        $this->Cell($width, $height, $text, $border, 0, $align, $fill);
    }
    
    public function newLine($height = 0){
        $this->Ln($height);
    }
    
    public function send($filename){
        $this->Output('I', $filename . '.pdf', true);
    }
    
    public function download($filename){
        $this->Output('D', $filename . '.pdf', true);
    }
    
    public function makeTable(Columns $columns, Container $data, float $cellMargin = 0, int $rowHeight = 5, Container $rowsToFill = null){
        $oldCellMargin = $this->cMargin;
        $this->cMargin = $cellMargin;
        $aligns = array();
        $i = 0;
        while($i < $columns->length()){
            $column = $columns->get($i);
            $aligns[] = $column->getAlign();
            $i++;
        }
        $this->SetAligns($aligns);
        $this->setColumns($columns);
        $this->SetLineWidth(0.2);
        if($rowsToFill === null){
            $rowsToFill = new Container();
        }
        foreach($data->toArray() as $index => $row){
            if($rowsToFill->contains($index)){
                $this->Row($row, $rowHeight, true);
            }
            else{
                $this->Row($row, $rowHeight);
            }
        }
        $this->cMargin = $oldCellMargin;
    }
}

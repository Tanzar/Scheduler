<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\File;

use libs\fpdf\FPDFExtended as FPDF;
use Tanweb\Utility as Utility;

/**
 * Adapter to FPDF, use to create pdf files
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class PDFMaker {
    private $pdf;
    private $font = 'arial';
    private $size = 9;
    private $style = '';
    
    public function __construct($orientation, $size){
        $this->pdf = new FPDF($orientation, 'mm', $size);
        $this->addPage($orientation);
        $this->pdf->setFont($this->font, $this->style, $this->size);
    }
    
    public function setCurrentFont($font){
        if(isset($font) && Utility::isString($font)){
            $this->font = $font;
        }
        $this->pdf->setFont($this->font, $this->style, $this->size);
    }
    
    public function setCurrentSize($size){
        if(isset($size) && Utility::isNumeric($size)){
            $this->size = $size;
        }
        $this->pdf->setFont($this->font, $this->style, $this->size);
    }
    
    //--- setCurrentStyle ---//
    public function setCurrentStyle($style){
        if($this->isAcceptableStyle($style)){
            $this->style = $style;
        }
        $this->pdf->setFont($this->font, $this->style, $this->size);
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
    
    public function addPage($orientation){
        if(Utility::inArray(["P", "L", ''], $orientation)){
            $this->pdf->AddPage($orientation);
        }
        else{
            $this->throwError('Wrong orientation, only P, L and empty string accepted');
        }
    }
    //--- setCurrentStyle ---//
    
    public function setMargin($margin, $size){
        switch($margin){
            case 'top':
                $this->pdf->SetTopMargin($size);
                break;
            case 'left':
                $this->pdf->SetLeftMargin($size);
                break;
            case 'right':
                $this->pdf->SetRightMargin($size);
                break;
            case 'bottom':
                $this->pdf->SetAutoPageBreak(true, $size);
                break;
            case 'all':
                $this->pdf->SetMargins($size, $size, $size);
                $this->pdf->SetAutoPageBreak(true, $size);
                break;
        }
    }
    
    public function setTextColor($red = 0, $green = 0, $blue = 0){
        $this->pdf->SetTextColor($red, $green, $blue);
    }
    
    public function setDrawColor($red = 255, $green = 255, $blue = 255){
        $this->pdf->SetDrawColor($red, $green, $blue);
    }
    
    public function setFillColor($red = 255, $green = 255, $blue = 255){
        $this->pdf->SetFillColor($red, $green, $blue);
    }
    
    public function setLineWidth($width){
        $this->pdf->SetLineWidth($width);
    }
    
    public function writeMulticell($width, $height, $text, $border = 0, $align = 'L'){
        if($height === 0){
            $height = $this->size;
        }
        $this->pdf->MultiCell($width, $height, $text, $border, $align);
    }
    
    public function writeCell($width, $height, $text, $border = 0, $align = 'L'){
        if($height === 0){
            $height = $this->size;
        }
        $this->pdf->Cell($width, $height, $text, $border, 0, $align);
    }
    
    public function newLine($height = 0){
        $this->pdf->Ln($height);
    }
    
    public function send($filename){
        $this->pdf->Output('I', $filename . '.pdf');
    }
    
    public function download($filename){
        $this->pdf->Output('D', $filename . '.pdf');
    }
    
    public function makeTable($widths, $data, $headers = array()){
        $aligns = array();
        foreach ($widths as $width){
            $aligns[] = 'C';
        }
        $this->pdf->SetAligns($aligns);
        $this->pdf->SetWidths($widths);
        $this->pdf->SetLineWidth(0.5);
        $this->pdf->Row($headers);
        $this->pdf->SetLineWidth(0.2);
        foreach($data as $row){
            $this->pdf->Row($row);
        }
    }
    
    private function throwError($msg){
        throw new Exception('PDFMaker error: ' . $msg);
    }
}

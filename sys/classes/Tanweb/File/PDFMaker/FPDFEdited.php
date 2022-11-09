<?php
namespace Tanweb\File\PDFMaker;

use libs\fpdf\FPDF as FPDF;
use Tanweb\File\PDFMaker\Column as Column;
use Tanweb\File\PDFMaker\Columns as Columns;


class FPDFEdited extends FPDF {
    private $aligns;
    private Columns $cols;
    
    protected function setColumns(Columns $cols) : void {
        $this->cols = $cols;
    }
    
    function SetAligns(array $aligns) {
        //Set the array of column alignments
        $this->aligns= $aligns;
    }
    
    protected function headerRow($data, int $height = 5, bool $fill = false) {
        //Calculate the height of the row
        $nb=0;
        for($i = 0; $i < $this->cols->length(); $i++){
            $column = $this->cols->get($i);
            $width = $column->getWidth();
            $text = '';
            if(isset($data[$i])){
                $text = $data[$i];
            }
            $nb=max($nb,$this->NbLines($width ,$text));
        }
        $h=5*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i<$this->cols->length();$i++)
        {
            $col = $this->cols->get($i);
            $w=$col->getWidth();
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Print the text
            $text = '';
            if(isset($data[$i])){
                $text = $data[$i];
            }
            if($fill || $col->doFill()){
                $this->MultiCell($w,$height,$data[$i],0,$a, true);
            }
            else{
                $this->MultiCell($w,$height,$data[$i],0,$a, false);
            }
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        $this->Ln($h);
    }

    function Row($data, int $height = 5, bool $fill = false) {
        //Calculate the height of the row
        $nb=0;
        for($i = 0; $i < $this->cols->length(); $i++){
            $column = $this->cols->get($i);
            $width = $column->getWidth();
            $key = $column->getKey();
            $text = '';
            if(isset($data[$key])){
                $text = $data[$key];
            }
            $nb=max($nb,$this->NbLines($width ,$text));
        }
        $h=$height*$nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for($i=0;$i< $this->cols->length() ; $i++)
        {
            $col = $this->cols->get($i);
            $w=$col->getWidth();
            $key = $col->getKey();
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Print the text
            $text = '';
            if(isset($data[$key])){
                $text = $data[$key];
            }
            if($fill || $col->doFill()){
                $this->MultiCell($w,$height,$text,0,$a, true);
            }
            else{
                $this->MultiCell($w,$height,$text,0,$a, false);
            }
            //Draw the border
            $this->Rect($x,$y,$w,$h);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        $this->Ln($h);
    }
    function CheckPageBreak($h) {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    
    function NbLines($w,$txt) {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}
?>
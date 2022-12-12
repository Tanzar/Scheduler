<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\File\PDFMaker;

use Tanweb\File\PDFMaker\Column as Column;

/**
 * Description of Columns
 *
 * @author Tanzar
 */
class Columns {
    private array $cols;
    
    public function __construct() {
        $this->cols = array();
    }
    
    public function add(Column $col) : void {
        $this->cols[] = $col;
    }
    
    public function get(int $index) : Column {
        if(isset($this->cols[$index])){
            return $this->cols[$index];
        }
        else{
            return new Column(0, '');
        }
    }
    
    public function length() : int {
        return (int) count($this->cols);
    }
    
    public function totalWidth() : float {
        $sum = 0;
        foreach ($this->cols as $col) {
            $sum += $col->getWidth();
        }
        return $sum;
    }
}

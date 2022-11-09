<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\File\PDFMaker;

/**
 * Description of Column
 *
 * @author Tanzar
 */
class Column {
    private float $width;
    private string $key;
    private bool $fill;
    private string $align;
    
    public function __construct(float $width, string $key, bool $fill = false) {
        $this->width = $width;
        $this->key = $key;
        $this->fill = $fill;
        $this->align = 'C';
    }
    
    public function getWidth() : float {
        return $this->width;
    }
    
    public function getKey() : string {
        return $this->key;
    }
    
    public function doFill() : bool {
        return $this->fill;
    }
    
    public function getAlign() : string {
        return $this->align;
    }
    
    public function setAlignCenter() : void {
        $this->align = 'C';
    }
    
    public function setAlignLeft() : void {
        $this->align = 'L';
    }
    
    public function setAlignRight() : void {
        $this->align = 'R';
    }
}

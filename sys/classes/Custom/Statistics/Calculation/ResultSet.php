<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Calculation;

use Custom\Statistics\Calculation\ResultItem as ResultItem;

/**
 * Description of ResultSet
 *
 * @author Tanzar
 */
class ResultSet {
    private array $result = array();
    
    public function __construct() {
        
    }
    
    public function add(ResultItem $item) : void {
        $notFound = true;
        foreach ($this->result as $index => $element) {
            if($item->isEqual($element)){
                $notFound = false;
                $this->result[$index]->add($item->getValue());
            }
        }
        if($notFound){
            $this->result[] = $item;
        }
    }
    
    public function getValue(array $keysValues) : float {
        $item = new ResultItem($keysValues);
        foreach ($this->result as $element) {
            if($item->isEqual($element)){
                return $element->getValue();
            }
        }
        return 0;
    }
    
    public function toArray() : array {
        return $this->result;
    }
}

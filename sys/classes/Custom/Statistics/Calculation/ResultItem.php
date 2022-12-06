<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Calculation;

/**
 * Description of ResultItem
 *
 * @author Tanzar
 */
class ResultItem {
    private array $keys = array();
    private float $value;
    
    public function __construct(array $keys, float $value = 0) {
        $this->keys = array();
        foreach ($keys as $index => $key) {
            $this->keys[$index] = $key;
        }
        $this->value = $value;
    }
    
    public function getKeys(): array {
        return $this->keys;
    }

    public function getValue() : float {
        return $this->value;
    }
    
    public function add(float $value) : void {
        $this->value += $value;
    }
    
    public function isEqual(ResultItem $compared) : bool {
        $comparedKeys = $compared->getKeys();
        if(count($comparedKeys) != count($this->keys)){
            return false;
        }
        foreach ($this->keys as $index => $keyValue) {
            $comparedKeyValue = $comparedKeys[$index];
            if($comparedKeyValue != $keyValue){
                return false;
            }
        }
        return true;
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Calculation\Counter;

/**
 * Description of Counter
 *
 * @author Tanzar
 */
class Counter {
    private array $values = array();
    
    public function __construct() {
        
    }
    
    public function addCounter(string $name, int $limit) : void {
        $notExists = true;
        if($limit < 1){
            $limit = 1;
        }
        foreach ($this->values as $key => $item) {
            if($item['name'] === $name){
                $notExists = false;
                $this->values[$key] = array(
                    'name' => $name,
                    'value' => 0,
                    'limit' => $limit
                );
            }
        }
        if($notExists){
            $this->values[] = array(
                'name' => $name,
                'value' => 0,
                'limit' => $limit
            );
        }
    }
    
    /**
     * increases counters from last to first
     * 
     * @return bool - true if resets
     */
    public function increase() : bool {
        $lastIndex = count($this->values) - 1;
        if($lastIndex > -1){
            return $this->increment($lastIndex);
        }
        return false;
    }
    
    private function increment(int $index) : bool {
        if($index === 0){
            if($this->values[0]['value'] >= $this->values[0]['limit']){
                $this->clear();
                return true;
            }
            else{
                $this->values[0]['value']++;
            }
        }
        else{
            if($this->values[$index]['value'] >= $this->values[$index]['limit']){
                $this->values[$index]['value'] = 0;
                return $this->increment($index - 1);
            }
            else{
                $this->values[$index]['value']++;
            }
        }
        return false;
    }
    
    private function clear() {
        foreach ($this->values as $key => $item) {
            $this->values[$key]['value'] = 0;
        }
    }
    
    public function getState() : array {
        $result = array();
        foreach ($this->values as $item) {
            $value = $item['value'];
            $name = $item['name'];
            $result[$name] = $value;
        }
        return $result;
    }
}

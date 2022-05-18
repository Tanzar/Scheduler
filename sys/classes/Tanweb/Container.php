<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb;

use Exception;

/**
 * Class to replace basic array usage, attempt to make it simillar to java Lists
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Container {
    private array $data;
    
    public function __construct(array $data = null) {
        if(isset($data)){
            $this->data = $data;
        }
        else{
            $this->data = array();
        }
    }
    
    public function isValueSet(string $key){
        if(isset($this->data[$key])){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function getValue(string $key){
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        else{
            $this->throwException('value not defined for key: ' . $key);
        }
    }
    
    public function getKeys() : array{
        return array_keys($this->data);
    }
    
    public function add($item, $key = null, bool $override = false){
        if(isset($key)){
            $this->addAsKey($item, $key, $override);
        }
        else{
            $this->data[] = $item;
        }
    }
    
    private function addAsKey($item, $key, bool $override){
        if($override){
            $this->data[$key] = $item;
        }
        else{
            if(isset($this->data[$key])){
                $this->throwException('cannot add item, key ' . $key . ' already exists.');
            }
            else{
                $this->data[$key] = $item;
            }
        }
    }
    
    public function contains($value) : bool {
        return in_array($value, $this->data);
    }
    
    public function getLength() : int{
        return Utility::count($this->data);
    }
    
    public function isEmpty() : bool {
        $length = $this->getLength();
        if($length === 0){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function toArray(){
        return $this->data;
    }
    
    private function throwException($msg){
        throw new Exception('Container error: ' . $msg);
    }
}

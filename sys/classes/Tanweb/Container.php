<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb;

use Tanweb\Database\DataFilter\Condition as Condition;
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
    
    public function get(string $key){
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
    
    public function remove($key){
        if(isset($this->data[$key])){
            unset($this->data[$key]);
        }
    }
    
    public function contains($value) : bool {
        return in_array($value, $this->data);
    }
    
    public function getKeyByValue($value, $variable = null){
        foreach ($this->data as $key => $item){
            if($variable === null){
                if($item === $value){
                    return $key;
                }
            }
            else{
                if($item[$variable] === $value){
                    return $key;
                }
            }
        }
        $this->throwException('value ' . $value . ' not found');
    }
    
    public function filter(Condition $condition) : Container {
        $result = new Container();
        foreach ($this->data as $item) {
            if(is_array($item) && $this->meetsCriteria($item, $condition)){
                $result->add($item);
            }
        }
        return $result;
    }
    
    private function meetsCriteria(array $item, Condition $condition) : bool {
        $key = $condition->getColumn();
        $val = $condition->getValue();
        $operation = $condition->getOperation();
        if(isset($item[$key])){
            switch ($operation) {
                case '=':
                    return $item[$key] === $val;
                case '>':
                    return $item[$key] > $val;
                case '<':
                    return $item[$key] < $val;
                case '<=':
                    return $item[$key] <= $val;
                case '>=':
                    return $item[$key] >= $val;
                case 'like':
                    return str_contains(trim($item[$key], '%'), $val);
                default:
                    return false;
            }
        }
        return false;
    }
    
    public function length() : int{
        return Utility::count($this->data);
    }
    
    public function isEmpty() : bool {
        $length = $this->length();
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

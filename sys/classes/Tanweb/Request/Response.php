<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Request;

use Tanweb\Container as Container;

/**
 * Class used by controllers to rend data back to browser
 * is disabled if opening controller in "file mode"
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Response {
    private Container $data;
    private $isDisabled;
    
    public function __construct(bool $disabe = false) {
        $this->isDisabled = $disabe;
        $this->data = new Container();
    }
    
    public function enable(){
        $this->isDisabled = false;
    }
    
    public function disable(){
        $this->isDisabled = true;
    }
    
    public function overrideData(Container $newData){
        if($this->isDisabled){
            $this->throwException('cannot override, Response is disabled.');
        }
        $this->data = $newData;
    }
    
    public function getData() {
        return $this->data;
    }
    
    public function toArray() : array {
        return $this->data->toArray();
    }
    
    public function isDisabled(){
        return $this->isDisabled;
    }
    
    public function isEnabled(){
        return !$this->isDisabled;
    }
    
    private function throwException($msg){
        throw new Exception('Response error: ' . $msg);
    }
}

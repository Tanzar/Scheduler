<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Request;

use Tanweb\Request\RequestException as RequestException;

/**
 * Class used by controllers to recieve data form browser requests
 * it requires 2 variables to be always send controller and task
 * controller is name of class whitch method will be called
 * task it that method
 * NOTE:
 * I used it to reduce number of script files and 
 * "standardize" how browser and server communicate
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Request {
    private array $data;
    private string $method;
    
    public function __construct(bool $check = true) {
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        switch($this->method){
            case 'POST':
                $this->data = filter_input_array(INPUT_POST);
                break;
            case 'GET':
                $this->data = filter_input_array(INPUT_GET);
                break;
            default:
                self::throwException('undefined request method, must be post or get.');
        }
        if($check){
            $this->checkRequired();
        }
    }
    
    private function checkRequired(){
        if(!isset($this->data['controller'])){
            $this->throwException('controller is not set.');
        }
        if(!isset($this->data['task'])){
            $this->throwException('task is not set.');
        }
    }
    
    public function getMethod() : string {
        return $this->method;
    }
    
    public function get($index){
        if(isset($this->data[$index])){
            return $this->data[$index];
        }
        else{
            $this->throwException('data not defined for: ' . $index);
        }
    }
    
    public function getController() : string {
        return $this->get('controller');
    }
    
    public function getTask() : string {
        return $this->get('task');
    }
    
    public function toJSON() :string {
        $str = 'Data: ' . json_encode($this->data);
        return $str;
    }
    
    private function throwException($msg){
        throw new RequestException('Request error: ' . $msg);
    }
}

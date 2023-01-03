<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Request;

use Tanweb\Request\RequestException as RequestException;
use Tanweb\Container as Container;

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
    private Container $data;
    private Container $files;
    private string $method;
    private string $controller;
    private string $task;
    
    public function __construct(bool $check = true) {
        $this->method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        switch($this->method){
            case 'POST':
                $data = filter_input_array(INPUT_POST);
                break;
            case 'GET':
                $data = filter_input_array(INPUT_GET);
                break;
            default:
                self::throwException('undefined request method, must be post or get.');
        }
        $this->filterData($data, $check);
        $this->loadFiles();
    }
    
    private function filterData(array $data, bool $check){
        if($check){
            $this->checkRequired($data);
            $this->controller = $data['controller'];
            $this->task = $data['task'];
        }
        $this->data = new Container();
        if(isset($data)){
            foreach($data as $key => $value){
                if($key !== 'task' && $key !== 'controller'){
                    $this->data->add($value, $key);
                }
            }
        }
    }
    
    private function checkRequired(array $data){
        if(!isset($data['controller'])){
            $this->throwException('controller is not set.');
        }
        if(!isset($data['task'])){
            $this->throwException('task is not set.');
        }
    }
    
    private function loadFiles() {
        $files = filter_var_array($_FILES);
        $this->files = new Container();
        if(count($files) > 0){
            foreach ($files as $file){
                $this->files->add($file);
            }
        }
    }
    
    public function getMethod() : string {
        return $this->method;
    }
    
    public function get(string $index = null){
        if(isset($index)){
            return $this->data->get($index);
        }
        else{
            return $this->data;
        }
    }
    
    public function getFile(string $index = null){
        if(isset($index)){
            return $this->files->get($index);
        }
        else{
            return $this->files;
        }
    }
    
    public function getController() : string {
        if(isset($this->controller)){
            return $this->controller;
        }
        else{
            return '';
        }
    }
    
    public function getTask() : string {
        if(isset($this->task)){
            return $this->task;
        }
        else{
            return '';
        }
    }
    
    public function toJSON() :string {
        $str = 'Controller: ' . $this->controller . ' Task: ' . $this->task
                . 'Data: ' . json_encode($this->data);
        return $str;
    }
    
    private function throwException($msg){
        throw new RequestException('Request error: ' . $msg);
    }
}

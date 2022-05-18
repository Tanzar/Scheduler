<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database\SQL;

use Tanweb\Database\SQL\SQLException;

/**
 * Base for building sql queries, no delete at the moment
 * 
 * @author Grzegorz Spakowski, Tanzar
 */
abstract class SqlBuilder {
    private $type;
    
    abstract public function select(string $table, array $fileds = array()) : SqlBuilder;
    
    abstract public function where(string $column, string $value, string $operator = '=') : SqlBuilder;
    
    abstract public function and() : SqlBuilder;
    
    abstract public function or() : SqlBuilder;
    
    abstract public function orderBy(string $column) : SqlBuilder;
    
    abstract public function insert(string $table) : SqlBuilder;
    
    abstract public function into(string $column, string $value) : SqlBuilder;
    
    abstract public function update(string $table, string $column, string $value) : SqlBuilder;
    
    abstract public function set(string $column, string $value) : SqlBuilder;
    
    abstract public function formSQL() : string;
    
    public function getType() : string {
        return $this->type;
    }
    
    public function isSelect(){
        return $this->isType('select');
    }
    
    public function isNotSelect(){
        return $this->isNotType('select');
    }
    
    public function isInsert(){
        return $this->isType('insert');
    }
    
    public function isNotInsert(){
        return $this->isNotType('insert');
    }
    
    public function isUpdate(){
        return $this->isType('update');
    }
    
    public function isNotUpdate(){
        return $this->isNotType('update');
    }
    
    protected function setSelect(){
        if(isset($this->type)){
            $this->throwException('cannot set another type, use new SqlBuilder');
        }
        $this->type = 'select';
    }
    
    protected function setUpdate(){
        if(isset($this->type)){
            $this->throwException('cannot set another type, use new SqlBuilder');
        }
        $this->type = 'update';
    }
    
    protected function setInsert(){
        if(isset($this->type)){
            $this->throwException('cannot set another type, use new SqlBuilder');
        }
        $this->type = 'insert';
    }
    
    private function isType(string $type){
        if($this->type === $type){
            return true;
        }
        return false;
    }
    
    private function isNotType(string $type){
        if($this->type !== $type){
            return true;
        }
        return false;
    }
    
    protected function throwException($msg){
        throw new SQLException($msg);
    }

}

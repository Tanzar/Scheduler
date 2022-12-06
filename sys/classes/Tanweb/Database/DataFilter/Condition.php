<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database\DataFilter;

/**
 * Description of Condition
 *
 * @author Tanzar
 */
class Condition {
    private string $column;
    private string $operation;
    private string $value;
    
    private function __construct(string $column, string $operation, string $value) {
        $this->column = $column;
        $this->operation = $operation;
        $this->value = $value;
    }
    
    public function getColumn() : string {
        return $this->column;
    }
    
    public function getOperation() : string {
        return $this->operation;
    }
    
    public function getValue() : string {
        return $this->value;
    }
    
    public static function equal(string $column, string $value) : Condition {
        return new Condition($column, '=', $value);
    }
    
    public static function less(string $column, string $value) : Condition {
        return new Condition($column, '<', $value);
    }
    
    public static function lessOrEqual(string $column, string $value) : Condition {
        return new Condition($column, '<=', $value);
    }
    
    public static function more(string $column, string $value) : Condition {
        return new Condition($column, '>', $value);
    }
    
    public static function moreOrEqual(string $column, string $value) : Condition {
        return new Condition($column, '>=', $value);
    }
    
    public static function include(string $column, string $value) : Condition {
        return new Condition($column, 'like', '%' . $value . '%');
    }
    
    public static function startsWith(string $column, string $value) : Condition {
        return new Condition($column, 'like', '%' . $value);
    }
    
    public static function endsWidth(string $column, string $value) : Condition {
        return new Condition($column, 'like', $value . '%');
    }
    
}

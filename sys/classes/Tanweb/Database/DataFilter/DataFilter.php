<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Tanweb\Database\DataFilter;

use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\DataFilter\Condition as Condition;
use Tanweb\Container as Container;

/**
 * Description of DataFilter
 *
 * @author Tanzar
 */
class DataFilter {
    private string $table;
    private Container $conditions;
    
    public function __construct(string $table, Container $datafilters = null) {
        $this->table = $table;
        $this->conditions = new Container();
        if($datafilters !== null){
            $this->processDataFilters($datafilters);
        }
    }
    
    private function processDataFilters(Container $filters) : void {
        foreach ($filters->toArray() as $filter) {
            $condition = new Container($filter);
            $operation = $condition->get('operation');
            $column = $condition->get('column');
            $value = $condition->get('value');
            switch($operation){
                case '=':
                    $this->addCondition(Condition::equal($column, $value));
                    break;
                case '<':
                    $this->addCondition(Condition::less($column, $value));
                    break;
                case '>':
                    $this->addCondition(Condition::more($column, $value));
                    break;
                case 'contains':
                    $this->addCondition(Condition::include($column, $value));
                    break;
                case 'starts':
                    $this->addCondition(Condition::startsWith($column, $value));
                    break;
                case 'ends':
                    $this->addCondition(Condition::endsWidth($column, $value));
                    break;
            }
        }
    }
    
    public function addCondition(Condition $condition) : void {
        $this->conditions->add($condition);
    }
    
    public function generateSQL() : MysqlBuilder {
        $sql = new MysqlBuilder();
        $sql->select($this->table);
        $count = 0;
        foreach ($this->conditions->toArray() as $condition) {
            $column = $condition->getColumn();
            $operation = $condition->getOperation();
            $value = $condition->getValue();
            $sql->where($column, $value, $operation);
            $count++;
            if($count <= $this->conditions->length()){
                $sql->and();
            }
        }
        return $sql;
    }
}

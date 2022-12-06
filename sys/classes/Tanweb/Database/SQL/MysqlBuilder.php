<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database\SQL;

use Tanweb\Utility as Utility;
use Tanweb\Database\SQL\SqlBuilder as SqlBuilder;

/**
 * Class used to build sql queries, use it to build sql queries in controllers
 * first use insert, select or update methods to set it into correct "mode"
 * than use other method to add values to insert, where to select etc.
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class MysqlBuilder extends SqlBuilder{
    private $table;
    private $orderByColumnss;
    private $whereConditions;
    private $selectColumns;
    private $insertColumns;
    private $updateColumns;
    private $updateCondition;
    private $deleteCondition;
    
    public function __construct() {
        $this->orderByColumnss = array();
        $this->whereConditions = array();
        $this->selectColumns = array();
        $this->insertColumns = array();
        $this->updateColumns = array();
    }
    
    /**
     * Sets Builder to build select query
     * 
     * @param string $table table on whitch select will be used
     * @param array $columns columns to select from table, if empty it will take all columns (*)
     * @return SqlBuilder
     */
    public function select(string $table, array $columns = []): SqlBuilder {
        $this->setSelect();
        $this->table = $table;
        $this->selectColumns = $columns;
        return $this;
    }
    
    /**
     * Adds condition to sql query
     * ! WORKS ONLY FOR SELECT, FOR UPDATE USE METHOD update() PARAMETERS !
     * ! IF YOU WANT TO USE MUTLIPLE CONDITIONS CALL METHODS and(), or() BEFORE !
     * 
     * @param string $column column for condition
     * @param string $value
     * @param string $operator operator to use, by default it uses '='
     * @return SqlBuilder
     */
    public function where(string $column, string $value, string $operator = '='): SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method where only works for sqlect sql, '
                    . 'for update set conditions in update() method.');
        }
        $last = end($this->whereConditions);
        if($last === false || Utility::isString($last)){
        $item = array(
            'column' => $column,
            'operator' => $operator,
            'value' => $value
        );
        $this->whereConditions[] = $item;
        return $this;
        }
        else{
            $this->throwException('cannot add condition, call and() or or() first.');
        }
    }

    /**
     * adds and in where for select query
     * ! ONLY WORKS FOR SELECT AND CAN ONLY BE USED AFTER where() METHOD !
     * 
     * @return SqlBuilder
     */
    public function and(): SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method only works for select.');
        }
        $last = end($this->whereConditions);
        if($last === false){
            $this->throwException('call where() before and().');
        }
        if(Utility::isString($last)){
            if($last === 'and' || $last === 'or' || $last === '('){
                $this->throwException('cannot use method after and(), or(), openBracket() methods');
            }
            else{
                $this->whereConditions[] = 'and';
            }
        }
        else{
            $this->whereConditions[] = 'and';
        }
        return $this;
    }

    /**
     * adds or in where for select query
     * ! ONLY WORKS FOR SELECT AND CAN ONLY BE USED AFTER where() METHOD !
     * 
     * @return SqlBuilder
     */
    public function or(): SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method only works for select');
        }
        $last = end($this->whereConditions);
        if($last === false){
            $this->throwException('call where() before or()');
        }
        if(Utility::isString($last)){
            if($last === 'and' || $last === 'or' || $last === '('){
                $this->throwException('cannot use method after and(), or(), openBracket() methods');
            }
            else{
                $this->whereConditions[] = 'or';
            }
        }
        else{
            $this->whereConditions[] = 'or';
        }
        return $this;
    }

    /**
     * Adds opening bracket '(' character for select query conditions
     * @return SqlBuilder
     */
    public function openBracket() : SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method only works for select');
        }
        $this->whereConditions[] = '(';
        return $this;
    }
    
    /**
     * Adds closing bracket ')' character for select query conditions
     * !it won't check if there were opening brackets before!
     * @return SqlBuilder
     */
    public function closeBracket() : SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method only works for select');
        }
        $last = end($this->whereConditions);
        if($last === false){
            $this->throwException('call where() before closeBracket()');
        }
        if(Utility::isString($last)){
            if($last === 'and' || $last === 'or' || $last === '('){
                $this->throwException('cannot use method after and(), or(), openBracket() methods');
            }
            else{
                $this->whereConditions[] = ')';
            }
        }
        else{
            $this->whereConditions[] = ')';
        }
        return $this;
    }
    
    
    /**
     * Adds order by to sql
     * ! CALL select() FIRST !
     * 
     * @param string $column
     * @return SqlBuilder
     */
    public function orderBy(string $column, bool $asc = true): SqlBuilder {
        if($this->isNotSelect()){
            $this->throwException('method orderBy only works for select sql.');
        }
        $item = array(
            'column' => $column
        );
        if($asc){
            $item['order'] = 'ASC';
        }
        else{
            $item['order'] = 'DESC';
        }
        $this->orderByColumnss[] = $item;
        return $this;
    }
    
    /**
     * Set builder build insert query
     * 
     * @param string $table -  table name for query
     * @return SqlBuilder
     */
    public function insert(string $table): SqlBuilder {
        $this->setInsert();
        $this->table = $table;
        return $this;
    }

    /**
     * Adds values and columns to insert query
     * ! CALL insert() FIRST !
     * 
     * @param string $column name of column where value will be inserted
     * @param string $value value to insert
     * @return SqlBuilder
     */
    public function into(string $column, string $value): SqlBuilder {
        if($this->isNotInsert()){
            $this->throwException('method into only works for insert sql.');
        }
        $item = array(
            'column' => $column,
            'value' => $value
        );
        $this->insertColumns[] = $item;
        return $this;
    }
    
    /**
     * Sets builder to build update query
     * 
     * @param string $table table to update
     * @param string $column column for where condition
     * @param string $value value for where condition
     * @return SqlBuilder
     */
    public function update(string $table, string $column, string $value): SqlBuilder {
        $this->setUpdate();
        $this->table = $table;
        $this->updateCondition = $column . " = '" . $value . "'";
        return $this;
    }
    
    /**
     * Used to add columns for update query
     * ! CALL update() FIRST !
     * 
     * @param string $column
     * @param string $value
     * @return SqlBuilder
     */
    public function set(string $column, string $value): SqlBuilder {
        if($this->isNotUpdate()){
            $this->throwException('method set only works for update sql.');
        }
        $item = array(
            'column' => $column,
            'value' => $value
        );
        $this->updateColumns[] = $item;
        return $this;
    }

    public function delete(string $table, string $column, string $value) : SqlBuilder {
        $this->setDelete();
        $this->table = $table;
        $this->deleteCondition = $column . " = '" . $value . "'";
        return $this;
    }
    
    /**
     * use to turn Builder into string
     * 
     * @return string combines settings into sql query
     */
    public function formSQL(): string {
        switch($this->getType()){
            case 'select':
                return $this->formSelect();
            case 'insert':
                return $this->formInsert();
            case 'update':
                return $this->formUpdate();
            case 'delete':
                return $this->formDelete();
            default:
                $this->throwException('wrong call, call select, insert or update first');
                break;
        }
    }
    
    private function formSelect() : string {
        $sql = 'SELECT ';
        $sql .= $this->formSelectColumns();
        $sql .= ' FROM ' . $this->table;
        $sql .= $this->formSelectWhere();
        $sql .= $this->formOrderBy();
        return $sql;
    }
    
    private function formSelectColumns() : string{
        $columns = '';
        if(Utility::count($this->selectColumns) === 0){
            return '*';
        }
        foreach($this->selectColumns as $i => $column){
            if($i === 0){
                $columns .= $column;
            }
            else{
                $columns .= ', ' . $column;
            }
        }
        return $columns;
    }
    
    private function formSelectWhere() : string {
        if(Utility::count($this->whereConditions) > 0){
            return $this->formWhere();
        }
        else{
            return '';
        }
    }
    
    private function formWhere() : string {
        $result = ' WHERE ';
        $last = array_key_last($this->whereConditions);
        foreach ($this->whereConditions as $i => $item){
            if($i !== $last){
                $result .= $this->formWhereParseItem($item);
            }
            else{
                $result .= $this->formWhereLastItem($item);
            }
        }
        return $result;
    }
    
    private function formWhereParseItem($item){
        if(Utility::isNotString($item)){
            if($item['operator'] === 'like'){
                return ' ' . $item['column'] . ' ' . 
                        $item['operator'] . " '%" . $item['value'] . "%'";
            }
            else{
                return ' ' . $item['column'] . ' ' . 
                        $item['operator'] . " '" . $item['value'] . "'";
            }
        }
        else{
            return ' ' . $item;
        }
    }
    
    private function formWhereLastItem($item){
        if(Utility::isNotString($item)){
            if($item['operator'] === 'like'){
                return ' ' . $item['column'] . ' ' . 
                        $item['operator'] . " '%" . $item['value'] . "%'";
            }
            else{
                return ' ' . $item['column'] . ' ' . 
                        $item['operator'] . " '" . $item['value'] . "'";
            }
        }
        else{
            if($item === ')'){
                return ')';
            }
            else{
                return '';
            }
        }
    }
    
    private function formOrderBy() : string {
        $result = '';
        if(Utility::count($this->orderByColumnss) > 0){
            $result = ' ORDER BY ';
            foreach ($this->orderByColumnss as $i => $item){
                $column = $item['column'];
                $order = $item['order'];
                if($i === 0){
                    $result .= $column . ' ' . $order;
                }
                else{
                    $result .= ', ' . $column . ' ' . $order;
                }
            }
        }
        return $result;
    }
    
    private function formInsert() : string {
        if(Utility::count($this->insertColumns) === 0){
            $this->throwException('call into() before formSQL()');
        }
        $sql = 'INSERT INTO ' . $this->table . ' ';
        $columns = '';
        $values = '';
        foreach ($this->insertColumns as $i => $item){
            if($i === 0){
                $columns .= $item['column'];
                $values .= "'" . $item['value'] . "'";
            }
            else{
                $columns .= ', ' . $item['column'];
                $values .= ", '" . $item['value'] . "'";
            }
        }
        $sql .= '(' . $columns . ') VALUES (' . $values . ')';
        return $sql;
    }
    
    private function formUpdate() : string {
        if(Utility::count($this->updateColumns) === 0){
            $this->throwException('call set() before formSQL()');
        }
        $sql = 'UPDATE ' . $this->table . ' SET ';
        foreach ($this->updateColumns as $i => $item){
            if($i === 0){
                $sql .= $item['column'] . "='" . $item['value'] . "'";
            }
            else{
                $sql .= ', ' . $item['column'] . "='" . $item['value'] . "'";
            }
        }
        $sql .= ' WHERE ' . $this->updateCondition;
        return $sql;
    }
    
    private function formDelete() : string {
        $sql = 'DELETE FROM ' . $this->table . 
                ' WHERE ' . $this->deleteCondition;
        return $sql;
    }
}

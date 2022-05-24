<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Containers;

use Data\Entities\Entity as Entity;
use Tanweb\Utility as Utility;
use Exception;

/**
 * Description of DataContainer
 *
 * @author Tanzar
 */
abstract class DataContainer{
    private array $items;
    private string $itemsType;
    
    public function __construct() {
        $this->items = array();
        $this->itemsType = $this->setItemsType();
    }
    
    protected abstract function setItemsType() : string;
    
    public function add(Entity $item){
        if($this->isCorrectType($item)){
            $this->items[] = $item;
        }
    }
    
    public function get(int $index) : Entity {
        if(isset($this->items[$index])){
            return $this->items[$index];
        }
        else{
            throw new Exception('Index: ' . $index . ' not found.');
        }
    }
    
    public function toArray() : array{
        return $this->items;
    }
    
    public function forEach(callable $function) : void{
        foreach ($this->items as $index => $item){
            $function($index, $item);
        }
    }
    
    /**
     * to mimic java style generic types
     */
    private function isCorrectType(Entity $item) : bool{
        return Utility::isInstance($item, $this->itemsType);
    }
}

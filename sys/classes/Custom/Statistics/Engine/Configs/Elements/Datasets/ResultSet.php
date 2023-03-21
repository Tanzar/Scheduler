<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Datasets;

use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Tanweb\Container as Container;
/**
 * Description of ResultSet
 *
 * @author Tanzar
 */
class ResultSet {
    private Container $data;
    private array $cases;
    
    public function __construct() {
        $this->data = new Container();
        $this->cases = Groups::cases();
    }
    
    public function addValue(GroupsContainer $groups, int $value) : void {
        $indexes = $this->formIndexes($groups);
        foreach ($indexes as $index) {
            if($this->data->isValueSet($index)){
                $newValue = (int) $this->data->get($index) + $value;
                $this->data->add($newValue, $index, true);
            }
            else{
                $this->data->add($value, $index, true);
            }
        }
    }
    
    private function formIndexes(GroupsContainer $groups) : array {
        $arr = $this->getActiveIndexes($groups);
        // initialize by adding the empty set
        $combinations = array(array( ));

        foreach ($arr as $element){
            foreach ($combinations as $combination){
                array_push($combinations, array_merge(array($element), $combination));
            }
        }
        return $this->filterCombinations($combinations);
    }
    
    private function getActiveIndexes(GroupsContainer $groups) : array {
        $arr = array();
        foreach ($this->cases as $item) {
            $value = $this->getGroupValue($item, $groups);
            if($value !== ''){
                $arr[] = $item->name . '=' . $value;
            }
        }
        return $arr;
    }
    
    private function filterCombinations(array $combinations) : array {
        $result = array();
        foreach ($combinations as $arr) {
            $text = '';
            foreach ($arr as $item) {
                $text = $item . ';' . $text;
            }
            if($text !== ''){
                $result[] = $text;
            }
        }
        return $result;
    }
    
    public function get(GroupsContainer $groups) : int {
        $index = $this->formIndex($groups);
        if($this->data->isValueSet($index)){
            return $this->data->get($index);
        }
        else{
            return 0;
        }
    }
    
    private function formIndex(GroupsContainer $groups) : string {
        $index = '';
        foreach ($this->cases as $item) {
            $value = $this->getGroupValue($item, $groups);
            if($value !== ''){
                $index .= $item->name . '=' . $value . ';';
            }
        }
        return $index;
    }
    
    private function getGroupValue(Groups $group, GroupsContainer $groups) : string {
        $value = '';
        foreach ($groups->toArray() as $item) {
            $type = $item->getType();
            if($group === $type){
                $value = $item->getValue();
            }
        }
        return $value;
    }
    
    public function toArray() : array {
        return $this->data->toArray();
    }
}

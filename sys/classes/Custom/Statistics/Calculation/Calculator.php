<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Calculation;

use Custom\Statistics\Calculation\ResultSet as ResultSet;
use Custom\Statistics\Calculation\ResultItem as ResultItem;
use Tanweb\Container as Container;
use Custom\Statistics\Options\Group as Group;

/**
 * Description of Calculator
 *
 * @author Tanzar
 */
class Calculator {
    
    public static function sum(Container $data, Container $groups, string $columnToSum) : ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $dataRow = new Container($item);
            $keys = array();
            foreach ($groups->toArray() as $group) {
                if($group === Group::Users){
                    $value = $group->getValue($dataRow);
                }
                else{
                    $value = (int) $group->getValue($dataRow);
                }
                $keys[$group->getColumn()] = $value;
            }
            $value = (float) $dataRow->get($columnToSum);
            $resultItem = new ResultItem($keys, $value);
            $result->add($resultItem);
        }
        return $result;
    }
    
    public static function count(Container $data, Container $groups) : ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item){
            $dataRow = new Container($item);
            $keys = array();
            foreach ($groups->toArray() as $group) {
                if($group === Group::Users){
                    $value = $group->getValue($dataRow);
                }
                else{
                    $value = (int) $group->getValue($dataRow);
                }
                $keys[$group->getColumn()] = $value;
            }
            $resultItem = new ResultItem($keys, 1);
            $result->add($resultItem);
        }
        return $result;
    }
    
}

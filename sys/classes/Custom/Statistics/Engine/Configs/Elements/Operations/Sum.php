<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Operations;

use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\CalculationStrategy as CalculationStrategy;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Container as Container;

/**
 * Description of Sum
 *
 * @author Tanzar
 */
class Sum implements CalculationStrategy {
    
    public function calculate(Container $data, GroupsContainer $groups): ResultSet {
        $result = new ResultSet();
        foreach ($data->toArray() as $item) {
            $row = new Container($item);
            $grouping = $this->determineGrouping($row, $groups);
            $value = $this->getRowValue($row);
            $result->addValue($grouping, $value);
        }
        return $result;
    }
    
    private function determineGrouping(Container $row, GroupsContainer $groups) : GroupsContainer{
        $grouping = $groups->copy();
        foreach ($grouping->toArray() as $group) {
            $group->setValue($row);
        }
        return $grouping;
    }
    
    private function getRowValue(Container $row) : int {
        if($row->isValueSet('value')){
            return (int) $row->get('value');
        }
        else{
            return 0;
        }
    }
}

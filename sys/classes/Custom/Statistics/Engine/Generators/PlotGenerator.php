<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Generators;

use Custom\Statistics\Engine\Generators\StatsGenerator as StatsGenerator;
use Custom\Statistics\Engine\Configs\Elements\Groups\Factory\GroupsFactory as GroupsFactory;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSets as ResultSets;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Container as Container;

/**
 * Description of PlotGenerator
 *
 * @author Tanzar
 */
class PlotGenerator extends StatsGenerator {
    
    protected function formToOutput(Container $outputConfig): Container {
        $tracesGroup = $outputConfig->get('traces_group');
        $layout = $this->formLayout();
        if($tracesGroup === ''){
            $traces = $this->formSingleTrace($outputConfig);
        }
        else{
            $traces = $this->formMultipleTraces($outputConfig);
        }
        $result = new Container();
        $result->add($traces->toArray(), 'traces');
        $result->add($layout, 'layout');
        return $result;
    }
    
    private function formLayout() : array {
        return array(
            'title' => $this->getTitle(),
            'width' => 1100,
            'height' => 800
        );
    }
    
    private function formMultipleTraces(Container $outputConfig) : Container {
        $outputForm = $this->getOutputForm();
        $tracesGroup = $outputConfig->get('traces_group');
        $groupType = Groups::from($tracesGroup);
        $group = GroupsFactory::create($groupType);
        $inputs = $this->getInputs();
        $tracesOptions = $group->getOptions($inputs);
        $result = new Container();
        foreach ($tracesOptions->toArray() as $item) {
            $option = new Container($item);
            $optionGroup = GroupsFactory::create($groupType, $option->get('value'));
            $trace = $this->formTraceForMultiple($optionGroup, $option, $outputConfig);
            $trace->add($outputForm->getPlotlyType(), 'mode');
            $trace->add($outputForm->getPlotlyType(), 'type');
            $result->add($trace->toArray());
        }
        return $result;
    }
    
    private function formTraceForMultiple(Group $optionGroup, Container $option, Container $outputConfig) : Container {
        $resultSet = $this->getResultSet($outputConfig);
        $trace = new Container();
        $groupsOptions = $this->formAxisOptions($outputConfig);
        $grouping = new GroupsContainer();
        $grouping->add($optionGroup);
        $x = array();
        $y = array();
        $combinations = $this->getCombinations($groupsOptions);
        foreach ($combinations as $groupsArray){
            $groupsContainer = $grouping->copy();
            $title = '';
            foreach ($groupsArray as $item) {
                $type = $item['type'];
                $value = $item['value'];
                $title .= $item['title'] . ' ';
                $group = GroupsFactory::create($type, $value);
                $groupsContainer->add($group);
            }
            $value = $resultSet->get($groupsContainer);
            $x[] = $title;
            $y[] = $value;
        }
        $trace->add($x, 'x');
        $trace->add($y, 'y');
        $trace->add($option->get('title'), 'name');
        return $trace;
    }

    private function formSingleTrace(Container $outputConfig) : Container {
        $outputForm = $this->getOutputForm();
        $resultSet = $this->getResultSet($outputConfig);
        $trace = new Container();
        $groupsOptions = $this->formAxisOptions($outputConfig);
        $x = array();
        $y = array();
        $combinations = $this->getCombinations($groupsOptions);
        foreach ($combinations as $groupsArray){
            $groupsContainer = new GroupsContainer();
            $title = '';
            foreach ($groupsArray as $item) {
                $type = $item['type'];
                $value = $item['value'];
                $title .= $item['title'] . ' ';
                $group = GroupsFactory::create($type, $value);
                $groupsContainer->add($group);
            }
            $value = $resultSet->get($groupsContainer);
            $x[] = $title;
            $y[] = $value;
        }
        $trace->add($x, 'x');
        $trace->add($y, 'y');
        $trace->add($outputForm->getPlotlyType(), 'mode');
        $trace->add($outputForm->getPlotlyType(), 'type');
        return $trace;
    }

    private function formAxisOptions(Container $outputConfig) : array {
        $axis = $outputConfig->get('axis');
        $result = array();
        $inputs = $this->getInputs();
        foreach ($axis as $item) {
            $type = Groups::from($item);
            $group = GroupsFactory::create($type);
            $values = $group->getOptions($inputs);
            $tmp = array();
            foreach ($values->toArray() as $key => $value) {
                $item = array(
                    'title' => $value['title'],
                    'value' => $value['value'],
                    'type' => $type
                );
                $tmp[] = $item;
            }
            $result[] = $tmp;
        }
        return $result;
    }
    
    private function getCombinations(array $arrays) : array {
        $result = array(array());
        foreach (array_reverse($arrays) as $property => $property_values) {
            $tmp = array();
            foreach ($result as $result_item) {
                foreach ($property_values as $property_key => $property_value) {
                    $tmp[] = $result_item + array($property_key => $property_value);
                }
            }
            $result = $tmp;
        }
        foreach ($result as $index => $combination) {
            if(count($combination) < count($arrays)){
                unset($result[$index]);
            }
        }
        return $result;
    }
    
    private function getResultSet(Container $outputConfig) : ResultSet {
        $resultSets = $this->getResultSets();
        $index = $outputConfig->get('dataset');
        return $resultSets->get($index);
    }
}

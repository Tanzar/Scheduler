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
use Custom\Statistics\Engine\Configs\Types\OutputForms as OutputForms;
use Tanweb\Container as Container;

/**
 * Description of PiePlotGenerator
 *
 * @author Tanzar
 */
class PiePlotGenerator extends StatsGenerator {
    
    protected function formToOutput(Container $outputConfig): Container {
        $tracesGroup = $outputConfig->get('traces_group');
        if($tracesGroup === ''){
            $traces = $this->formSingleTrace($outputConfig);
        }
        else{
            $traces = $this->formMultipleTraces($outputConfig);
        }
        $layout = $this->formLayout($traces);
        $result = new Container();
        $result->add($traces->toArray(), 'traces');
        $result->add($layout, 'layout');
        return $result;
    }
    
    private function formLayout(Container $traces) : array {
        if($traces->length() > 1){
            return $this->formLayoutMultiple($traces);
        }
        else{
            return $this->formLayoutSingle();
        }
        
    }
    
    private function formLayoutSingle() : array {
        return array(
            'title' => $this->getTitle(),
            'width' => 1100,
            'height' => 700
        );
    }
    
    private function formLayoutMultiple(Container $traces) : array {
        $cols = ceil(sqrt($traces->length()));
        $rows = ceil($traces->length() / $cols);
        $row = 0;
        $col = 0;
        foreach ($traces->toArray() as $key => $item){
            $domain = array('row' => $row, 'column' => $col);
            $item['domain'] = $domain;
            $traces->add($item, $key, true);
            $col++;
            if($col >= $cols){
                $col = 0;
                $row++;
            }
        }
        $width = 1100;
        $annotations = $this->formAnnotations($traces, $cols, $rows, $width);
        return array(
            'title' => $this->getTitle(),
            'width' => $width,
            'height' => 800,
            'grid' => array(
                'rows' => $rows,
                'columns' => $cols
            ),
            'annotations' => $annotations
        );
    }
    
    private function formAnnotations(Container $traces, int $cols, int $rows, int $width) : array {
        $outputForm = $this->getOutputForm();
        if($outputForm === OutputForms::PieChart || $outputForm === OutputForms::RingPlot){
            $font = 15;
            $xPerTrace = 1 / $cols;
            $startX = 0;
            $yPerTrace = 1 / $rows;
            $startY = 1;
            $annotations = array();
            foreach ($traces->toArray() as $key => $item) {
                $item['domain'] = array(
                    'x' => array($startX + ($xPerTrace * 0.1), $startX + ($xPerTrace * 0.9)),
                    'y' => array($startY - ($yPerTrace * 0.9), $startY - ($yPerTrace * 0.1))
                );
                $traces->add($item, $key, true);
                $annotations[] = array(
                    'font' => array(
                        'family' => 'Arial',
                        'size' => $font
                    ),
                    'xanchor' => 'left',
                    'yanchor' => 'bottom',
                    'xref' => 'paper',
                    'showarrow' => false,
                    'text' => $item['name'],
                    'width' => 0.9 * $xPerTrace * $width,
                    'x' => $startX,
                    'y' => $startY - ($yPerTrace * 0.1)
                );
                $startX += $xPerTrace;
                if($startX >= 1){
                    $startX = 0;
                    $startY -= $yPerTrace;
                }
            }
            return $annotations;
        }
        else{
            return array();
        }
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
            if($outputForm === OutputForms::RingPlot){
                $trace->add('.4', 'hole');
            }
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
        $labels = array();
        $values = array();
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
            $labels[] = $title;
            $values[] = $value;
        }
        $trace->add($labels, 'labels');
        $trace->add($values, 'values');
        $trace->add($option->get('title'), 'name');
        return $trace;
    }

    private function formSingleTrace(Container $outputConfig) : Container {
        $outputForm = $this->getOutputForm();
        $resultSet = $this->getResultSet($outputConfig);
        $trace = new Container();
        $groupsOptions = $this->formAxisOptions($outputConfig);
        $labels = array();
        $values = array();
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
            $labels[] = $title;
            $values[] = $value;
        }
        $trace->add($labels, 'labels');
        $trace->add($values, 'values');
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

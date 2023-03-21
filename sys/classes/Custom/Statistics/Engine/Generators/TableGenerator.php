<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Generators;

use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSets as ResultSets;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Custom\Statistics\Engine\Generators\StatsGenerator as StatsGenerator;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Groups\Factory\GroupsFactory as GroupsFactory;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Tanweb\Container as Container;
/**
 * Description of TableGenerator
 *
 * @author Tanzar
 */
class TableGenerator extends StatsGenerator {
    private bool $produceHTML = true;
    
    protected function formToOutput(Container $outputConfig): Container {
        $resultSets = $this->getResultSets();
        $index = $outputConfig->get('dataset');
        $resultSet = $resultSets->get($index);
        $cols = $this->getGroupValues($outputConfig, 'cols');
        $rows = $this->getGroupValues($outputConfig, 'rows');
        $output = new Container();
        if($this->produceHTML){
            $html = $this->formHTML($resultSet, $cols, $rows, $outputConfig);
            $output->add($html, 'HTML');
        }
        else{
            $table = $this->formTable($resultSet, $cols, $rows, $outputConfig);
            $output->add($table, 'table');
        }
        return $output;
    }

    private function formHTML(ResultSet $resultSet, Container $cols, Container $rows, Container $outputConfig) : string {
        $html = '<table class="standard-table">';
        $html .= '<tr class="standard-table-tr"><td class="standard-table-td"'
                . ' colspan="' . $cols->length() + 2 . '">' . $this->getTitle() . '</td></tr>';
        $html .= $this->formTableBody($resultSet, $cols, $rows, $outputConfig);
        $html .= '</table>';
        return $html;
    }
    
    private function formTableBody(ResultSet $resultSet, Container $cols, Container $rows, Container $outputConfig) : string {
        $html = '<tr class="standard-table-tr"><td class="standard-table-td"></td>';
        foreach ($cols->toArray() as $col) {
            $html .= '<td class="standard-table-td">' . $col['title'] . '</td>';
        }
        $html .= '<td class="standard-table-td">Σ</td></tr>';
        foreach ($rows->toArray() as $row) {
            $html .= '<tr class="standard-table-tr">';
            $html .= '<td class="standard-table-td">' . $row['title'] . '</td>';
            $sum = 0;
            foreach ($cols->toArray() as $col) {
                $groups = $this->formGroupsContainer($outputConfig, $col['value'], $row['value']);
                $value = $resultSet->get($groups);
                if($value === 0){
                    $value = '';
                }
                else{
                    $sum += (int) $value;
                }
                $html .= '<td class="standard-table-td">' . $value . '</td>';
            }
            $html .= '<td class="standard-table-td">' . $sum . '</td>';
            $html .= '</tr>';
        }
        return $html;
    }
    
    private function formTable(ResultSet $resultSet, Container $cols, Container $rows, Container $outputConfig) : array {
        $result = array(
            'cells' => array(),
            'title' => $this->getTitle()
        );
        $x = 0;
        $result['cells'][$x] = array();
        $result['cells'][$x][] = '';
        foreach ($cols->toArray() as $col) {
            $result['cells'][$x][] = $col['title'];
        }
        $result['cells'][$x][] = 'Σ';
        $x++;
        foreach ($rows->toArray() as $row) {
            $result['cells'][$x] = array();
            $result['cells'][$x][] = $row['title'];
            $sum = 0;
            foreach ($cols->toArray() as $col) {
                $groups = $this->formGroupsContainer($outputConfig, $col['value'], $row['value']);
                $value = $resultSet->get($groups);
                if($value === 0){
                    $value = '';
                }
                else{
                    $sum += (int) $value;
                }
                $result['cells'][$x][] = $value;
            }
            $result['cells'][$x][] = $sum;
            $x++;
        }
        return $result;
    }
    
    private function formGroupsContainer(Container $outputConfig, string $colValue, string $rowValue) : GroupsContainer {
        $container = new GroupsContainer();
        $cols = $outputConfig->get('cols');
        $colsType = Groups::from($cols);
        $colsGroup = GroupsFactory::create($colsType, $colValue);
        $container->add($colsGroup);
        $rows = $outputConfig->get('rows');
        $rowsType = Groups::from($rows);
        $rowsGroup = GroupsFactory::create($rowsType, $rowValue);
        $container->add($rowsGroup);
        return $container;
    }
    
    private function getGroupValues(Container $outputConfig, string $var) : Container {
        $name = $outputConfig->get($var);
        $type = Groups::from($name);
        $group = GroupsFactory::create($type);
        $inputs = $this->getInputs();
        return $group->getOptions($inputs);
    }
    
    public function setToGenerateTable() : void {
        $this->produceHTML = false;
    }
}

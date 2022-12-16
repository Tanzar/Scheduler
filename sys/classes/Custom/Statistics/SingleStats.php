<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Tanweb\Container as Container;
use Custom\Statistics\Calculation\ResultSet as ResultSet;
use Custom\Statistics\Options\Group as Group;
use Custom\Statistics\Options\ResultForm as ResultForm;
use Custom\Statistics\Options\Method as Method;

/**
 * Description of SingleStats
 *
 * @author Tanzar
 */
class SingleStats extends Statistics {
    private string $title;
    
    public function __construct(Container $data, Container $inputsValues) {
        parent::__construct($data, $inputsValues);
        $this->title = $data->get('name');
    }
    
    public function generate(): Container {
        $data = $this->getDataset();
        $groupingColumns = $this->parseGroupingColumns();
        $json = $this->getJson();
        $method = Method::from($json->get('method'));
        $resultSet = $method->calculate($data, $groupingColumns);
        return $this->parseResultSet($resultSet);
    }

    private function parseGroupingColumns() : Container {
        $result = new Container();
        $json = $this->getJson();
        $x = $json->get('x');
        $group = Group::from($x);
        $result->add($group);
        if($json->isValueSet('y')){
            $y = $json->get('y');
            $group = Group::from($y);
            $result->add($group);
        }
        return $result;
    }
    
    private function parseResultSet(ResultSet $resultSet) : Container {
        $result = new Container();
        $result->add($this->title, 'title');
        $json = $this->getJson();
        $resultForm = ResultForm::from($json->get('resultForm'));
        if($resultForm === ResultForm::Table){
            $result->add('table', 'type');
            $this->addTableData($result, $resultSet);
        }
        else{
            $result->add('plot', 'type');
            $this->addPlotData($resultForm, $result, $resultSet);
        }
        return $result;
    }
    
    private function addTableData(Container $result, ResultSet $resultSet) : void {
        $inputValues = $this->getInputsValues();
        $json = $this->getJson();
        $x = $json->get('x');
        $y = $json->get('y');
        $rowsGroup = Group::from($y);
        $columnsGroup = Group::from($x);
        $columns = $columnsGroup->getOptions($inputValues);
        $cells = array();
        $cells[] = $this->formFirstRow($rowsGroup, $columns);
        $rowsOptions = $rowsGroup->getOptions($inputValues);
        foreach ($rowsOptions->toArray() as $options) {
            $cells[] = $this->formRow($rowsGroup, $columnsGroup, $columns, $options, $resultSet);
        }
        $result->add($cells, 'cells');
    }
    
    private function formFirstRow(Group $rowsGroup, Container $columns) : array {
        $row = array();
        $inputValues = $this->getInputsValues();
        if($rowsGroup === Group::Users){
            if(!$inputValues->isValueSet('year')){
                $row[] = 'Rok';
            }
            $row[] = 'SUZUG';
        }
        $row[] = $rowsGroup->value;
        if($rowsGroup === Group::Users){
            $row[] = 'Typ';
        }
        foreach ($columns->toArray() as $column) {
            $row[] = $column['title'];
        }
        return $row;
    }
    
    private function formRow(Group $rowsGroup, Group $columnsGroup, Container $columns, array $rowOptions, ResultSet $resultSet) : array {
        $row = array();
        $inputValues = $this->getInputsValues();
        if($rowsGroup === Group::Users){
            if(!$inputValues->isValueSet('year')){
                $row[] = $rowOptions['year'];
            }
            $row[] = $rowOptions['SUZUG'];
        }
        $row[] = $rowOptions['title'];
        if($rowsGroup === Group::Users){
            $row[] = $rowOptions['user_type'];
        }
        foreach ($columns->toArray() as $column) {
            $keysValues = array();
            $keysValues[$rowsGroup->getColumn()] = $rowOptions['value'];
            $keysValues[$columnsGroup->getColumn()] = $column['value'];
            $value = $resultSet->getValue($keysValues);
            if($value <= 0) {
                $row[] = '';
            }
            else{
                $row[] = $value;
            }
        }
        return $row;
    }
    
    private function addPlotData(ResultForm $resultForm, Container $result, ResultSet $resultSet) : void {
        $json = $this->getJson();
        $x = $json->get('x');
        $plotData = new Container();
        $type = $resultForm->getPlotlyType();
        $plotData->add($type, 'type');
        $mode = $resultForm->getPlotlyMode();
        if($mode !== ''){
            $plotData->add($mode, 'mode');
        }
        $group = Group::from($x);
        if($resultForm === ResultForm::PieChart || $resultForm === ResultForm::RingPlot){
            $this->formPiePlotValues($plotData, $resultSet, $group);
            if($resultForm === ResultForm::RingPlot){
                $plotData->add(0.4, 'hole');
            }
        }
        else{
            $this->formStandardPlotValues($plotData, $resultSet, $group);
        }
        $result->add($plotData->toArray(), 'data');
    }
    
    private function formPiePlotValues(Container $result, ResultSet $resultSet, Group $group) : void {
        $inputValues = $this->getInputsValues();
        $options = $group->getOptions($inputValues);
        $values = array();
        $labels = array();
        foreach ($options->toArray() as $item) {
            $option = new Container($item);
            $labels[] = $this->getOptionText($group, $option);
            $values[] = $this->getOptionValue($group, $option, $resultSet);
        }
        $result->add($values, 'values');
        $result->add($labels, 'labels');
    }
    
    
    
    private function formStandardPlotValues(Container $result, ResultSet $resultSet, Group $group) : void {
        $inputValues = $this->getInputsValues();
        $options = $group->getOptions($inputValues);
        $x = array();
        $y = array();
        foreach ($options->toArray() as $item) {
            $option = new Container($item);
            $x[] = $this->getOptionText($group, $option);
            $y[] = $this->getOptionValue($group, $option, $resultSet);
        }
        $result->add($x, 'x');
        $result->add($y, 'y');
    }
    
    private function getOptionText(Group $group, Container $option) : string {
        $result = '';
        $inputValues = $this->getInputsValues();
        if($group === Group::Users){
            if(!$inputValues->isValueSet('year')){
                $result .= $option->get('year') . ': ';
            }
            $result .= $option->get('SUZUG') . ' - ';
        }
        $result .= $option->get('title');
        return $result;
    }
    
    private function getOptionValue(Group $group, Container $option, ResultSet $resultset) : float {
        $key = $group->getColumn();
        $keysValues = array();
        $keysValues[$key] = $option->get('value');
        return $resultset->getValue($keysValues);
    }
}
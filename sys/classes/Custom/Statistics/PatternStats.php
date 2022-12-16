<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Config\Template as Template;
use Custom\Statistics\Options\DataSet as DataSet;
use Custom\Statistics\Calculation\Calculator as Calculator;
use Custom\Statistics\Calculation\ResultSet as ResultSet;
use Custom\Statistics\Options\Group as Group;
use Custom\Statistics\Options\Method as Method;
use Custom\Statistics\Options\Shift as Shift;
use Custom\Statistics\Options\Input as Input;

/**
 * Description of PatternStats
 *
 * @author Tanzar
 */
class PatternStats extends Statistics {
    private string $title;
    private Container $inputsValues;
    private ExcelEditor $xlsx;
    private Container $datasets;
    private Container $loadedDatasts;
    private Container $groupsOptions;
    private Container $loadedGroups;
    
    public function __construct(Container $data, Container $inputsValues) {
        parent::__construct($data, $inputsValues);
        $this->title = $data->get('name');
        $this->loadInputs($inputsValues);
        $this->datasets = new Container();
        $this->loadedDatasts = new Container();
        $this->groupsOptions = new Container();
        $this->loadedGroups = new Container();
    }
    
    private function loadInputs(Container $inputsValues){
        $json = $this->getJson();
        if($json->isValueSet('inputsOverride')){
            $inputsOverride = $json->get('inputsOverride');
            foreach ($inputsOverride as $key => $value) {
                $inputsValues->add($value, $key, true);
            }
        }
        $this->inputsValues = $inputsValues;
    }
    
    public function generate(): Container {
        $this->xlsx = new ExcelEditor();
        $json = $this->getJson();
        $filename = $json->get('file');
        $filepath = Template::getLocalPathInnerDir($filename);
        $this->xlsx->openFile($filepath);
        $this->fillFile();
        $this->xlsx->sendToBrowser($this->title);
        return new Container(['disabled' => true]);
    }
    
    private function fillFile() : void {
        $json = $this->getJson();
        $cols = $json->get('cols');
        foreach ($cols as $col) {
            $column = new Container($col);
            $this->writeColumnByConfig($column, $json);
        }
        $cells = $json->get('cells');
        foreach ($cells as $item) {
            $cell = new Container($item);
            $this->writeCellByConfig($cell);
        }
    }
    
    private function writeColumnByConfig(Container $column, Container $json) : void {
        $currentColumn = (int) $column->get('number');
        $rows = $json->get('rows');
        foreach ($rows as $row) {
            $rowConfig = new Container($row);
            $this->writeColumnCells($currentColumn, $column, $rowConfig);
        }
    }
    
    private function writeColumnCells(int $colNumber, Container $column, Container $row) : void {
        $currentRow = (int) $row->get('start');
        $end = (int) $row->get('end');
        $index = 0;
        while($currentRow <= $end){
            $address = $this->xlsx->getAddress($currentRow, $colNumber);
            if($column->isValueSet('dataset')){
                $value = $this->getValueFromDataset($index, $column, $row);
            }
            else{
                $value = $this->getValueFromGroup($index, $column, $row);
            }
            if($value !== 0 && $value !== '0' && $value !== ''){
                $sheetName = $this->xlsx->getCurrentSheetName();
                $this->xlsx->writeToCell($sheetName, $address, $value);
            }
            $index++;
            $currentRow++;
        }
    }
    
    private function getValueFromGroup(int $index, Container $column, Container $row) : string {
        $groupset = $column->get('groupset');
        $group = Group::from($groupset);
        $options = $this->getGroupsItems($group);
        if($row->isValueSet('value')){
            foreach ($options->toArray() as $item) {
                $option = new Container($item);
                $key = $column->get('groupsetKey');
                return $option->get($key);
            }
        }
        else{
            if($options->isValueSet($index)){
                $item = $options->get($index);
                $option = new Container($item);
                $key = $column->get('groupsetKey');
                return $option->get($key);
            }
        }
        return '';
    }
    
    private function getValueFromDataset(int $index, Container $column, Container $row) : string {
        $resultSet = $this->formResultSet($column, $row);
        $keys = $this->getResultSetKeys($index, $column, $row);
        return $resultSet->getValue($keys);
    }
    
    private function formResultSet(Container $column, Container $row) : ResultSet {
        $datasetName = $column->get('dataset');
        $dataset = DataSet::from($datasetName);
        $data = $this->getDatasetItems($dataset);
        $groups = new Container();
        $colGroups = $column->get('groups');
        foreach ($colGroups as $name) {
            $group = Group::from($name);
            $groups->add($group);
        }
        $rowGroup = $row->get('group');
        $group = Group::from($rowGroup);
        $groups->add($group);
        $method = Method::from($column->get('method'));
        return $method->calculate($data, $groups);
    }
    
    private function getResultSetKeys(int $index, Container $column, Container $row) : array {
        $result = array();
        $group = Group::from($row->get('group'));
        $options = $this->getGroupsItems($group);
        if($row->isValueSet('value')){
            $result[$group->getColumn()] = $row->get('value');
        }
        else{
            if($options->isValueSet($index)){
                $item = $options->get($index);
                $result[$group->getColumn()] = $item['value'];
            }
        }
        $columnGroups = $column->get('groups');
        $columnValues = $column->get('values');
        for($g = 0, $v = 0; $g < count($columnGroups) && $v < count($columnValues); $g++, $v++){
            $group = Group::from($columnGroups[$g]);
            $result[$group->getColumn()] = $columnValues[$v];
        }
        return $result;
    }
    
    private function writeCellByConfig(Container $config) : void {
        $row = (int) $config->get('row');
        $col = (int) $config->get('column');
        $address = $this->xlsx->getAddress($row, $col);
        $value = $this->getInputCellValue($config);
        $sheetName = $this->xlsx->getCurrentSheetName();
        if($value !== 0 && $value !== '0' && $value !== ''){
            $this->xlsx->writeToCell($sheetName, $address, $value);
        }
    }
    
    private function getInputCellValue(Container $config) : string {
        $inputName = $config->get('input');
        $input = Input::from($inputName);
        $operation = $config->get('operation');
        switch($operation){
            case 'text':
                return $this->getInputText($input);
            case 'value':
                return $this->getInputValue($input);
            case 'count':
                return $this->getInputCount($input);
        }
        return 0;
    }
    
    private function getInputText(Input $input) : string {
        $languages = Languages::getInstance();
        $months = new Container($languages->get('months'));
        if($input !== Input::MonthsRange && $input !== Input::YearsRange && $input !== Input::Month && $input !== Input::Year){
            $inputValue = $this->inputsValues->get($input->getVariableName());
            $value = $input->getTitleByValue($inputValue);
        }
        elseif($input === Input::Year){
            $value = $this->inputsValues->get($input->getVariableName());
        }
        elseif($input === Input::Month){
            $number = $this->inputsValues->get($input->getVariableName());
            $value = $months->get($number);
        }
        elseif($input === Input::MonthsRange){
            $start = $this->inputsValues->get('monthStart');
            $end = $this->inputsValues->get('monthEnd');
            $value = $months->get($start) . ' - ' . $months->get($end);
        }
        else{
            $start = $this->inputsValues->get('yearStart');
            $end = $this->inputsValues->get('yearEnd');
            $value = $start . ' - ' . $end;
        }
        return $value;
    }
    
    private function getInputValue(Input $input) : string {
        if($input !== Input::MonthsRange && $input !== Input::YearsRange && $input !== Input::Month){
            $value = $this->inputsValues->get($input->getVariableName());
        }
        elseif($input === Input::Month){
            $number = $this->inputsValues->get($input->getVariableName());
            $value = $number;
        }
        elseif($input === Input::MonthsRange){
            $start = $this->inputsValues->get('monthStart');
            $end = $this->inputsValues->get('monthEnd');
            $value = $start . ' - ' . $end;
        }
        else{
            $start = $this->inputsValues->get('yearStart');
            $end = $this->inputsValues->get('yearEnd');
            $value = $start . ' - ' . $end;
        }
        return $value;
    }
    
    private function getInputCount(Input $input) : string {
        if($input === Input::MonthsRange || $input === Input::YearsRange){
            if($input === Input::MonthsRange){
                $start = (int) $this->inputsValues->get('monthStart');
                $end = (int) $this->inputsValues->get('monthEnd');
            }
            else{
                $start = (int) $this->inputsValues->get('yearStart');
                $end = (int) $this->inputsValues->get('yearEnd');
            }
            return $end - $start + 1;
        }
        else{
            return $this->getInputValue($input);
        }
        return 0;
    }
    
    private function getDatasetItems(DataSet $dataset) : Container {
        if($this->loadedDatasts->contains($dataset->value)){
            return $this->datasets->get($dataset->value);
        }
        else{
            $data = $dataset->getData($this->inputsValues);
            $this->datasets->add($data, $dataset->value);
            $this->loadedDatasts->add($dataset->value);
            return $data;
        }
    }
    
    private function getGroupsItems(Group $group) : Container {
        if($this->loadedGroups->contains($group->value)){
            return $this->groupsOptions->get($group->value);
        }
        else{
            $data = $group->getOptions($this->inputsValues);
            $this->groupsOptions->add($data, $group->value);
            $this->loadedGroups->add($group->value);
            return $data;
        }
    }
}

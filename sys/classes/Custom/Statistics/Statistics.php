<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics;

use Custom\Statistics\Options\Type as Type;
use Custom\Statistics\Options\DataSet as DataSet;
use Custom\Statistics\Options\Input as Input;
use Tanweb\Database\DataFilter\DataFilter as DataFilter;
use Tanweb\Database\DataFilter\Condition as Condition;
use Tanweb\Container as Container;
/**
 * Description of Statistics
 *
 * @author Tanzar
 */
abstract class Statistics {
    private string $name;
    private Type $type;
    private Container $json;
    private Container $dataset;
    private Container $inputsValues;
    
    protected function __construct(Container $data, Container $inputsValues) {
        $this->name = $data->get('name');
        $this->type = Type::from($data->get('type'));
        $this->inputsValues = $inputsValues;
        $json = $data->get('json');
        $jsonArray = json_decode($json, true);
        $this->json = new Container($jsonArray);
        $this->recieveDataset($inputsValues);
    }
    
    private function recieveDataset(Container $inputsValues) : void {
        $datasetName = $this->json->get('dataset');
        $dataset = DataSet::from($datasetName);
        if($inputsValues->length() > 0){
            $filter = $this->processDatafilter($dataset, $inputsValues);
            $this->dataset = $dataset->getData($filter);
        }
        else{
            $this->dataset = $dataset->getData();
        }
    }

    private function processDatafilter(DataSet $dataset, Container $inputsValues) : DataFilter {
        $inputs = $this->json->get('inputs');
        $viewName = $dataset->getViewName();
        $filter = new DataFilter($viewName);
        foreach ($inputs as $name) {
            $input = Input::from($name);
            $this->processInput($input, $filter, $inputsValues);
        }
        return $filter;
    }
    
    private function processInput(Input $input, DataFilter $datafilter, Container $inputsValues) : void {
        switch ($input) {
            case Input::Month:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'month(date)');
                break;
            case Input::MonthsRange:
                $start = Condition::moreOrEqual('month(date)', $inputsValues->get('monthStart'));
                $datafilter->addCondition($start);
                $end = Condition::lessOrEqual('month(date)', $inputsValues->get('monthEnd'));
                $datafilter->addCondition($end);
                break;
            case Input::Year:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'year(date)');
                break;
            case Input::YearsRange:
                $start = Condition::moreOrEqual('year(date)', $inputsValues->get('yearStart'));
                $datafilter->addCondition($start);
                $end = Condition::lessOrEqual('year(date)', $inputsValues->get('yearEnd'));
                $datafilter->addCondition($end);
                break;
            case Input::User:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'username');
                break;
            case Input::Inspector:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'username');
                break;
            case Input::Location:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'location');
                break;
            case Input::LocationGroup:
                $this->addDatafilterEqualCondition($input, $datafilter, $inputsValues, 'location_group');
                break;
        }
    }
    
    private function addDatafilterEqualCondition(Input $input, DataFilter $datafilter, Container $inputsValues, string $column) : void {
        $variableName = $input->getVariableName();
        $condition = Condition::equal($column, $inputsValues->get($variableName));
        $datafilter->addCondition($condition);
    }

    protected function getName(): string {
        return $this->name;
    }

    protected function getType(): Type {
        return $this->type;
    }
    
    protected function getJson(): Container {
        return $this->json;
    }

    protected function getDataset(): Container {
        return $this->dataset;
    }
    public function getInputsValues(): Container {
        return $this->inputsValues;
    }

    public abstract function generate() : Container;
}

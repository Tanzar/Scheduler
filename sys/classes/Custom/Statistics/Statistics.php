<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics;

use Custom\Statistics\Options\Type as Type;
use Custom\Statistics\Options\DataSet as DataSet;
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
        if($this->json->isValueSet('dataset')){
            $this->dataset = $this->recieveDataset($inputsValues);
        }
        else{
            $this->dataset = new Container();
        }
    }
    
    private function recieveDataset(Container $inputsValues) : Container {
        $datasetName = $this->json->get('dataset');
        $dataset = DataSet::from($datasetName);
        if($inputsValues->length() > 0){
            return $dataset->getData($inputsValues);
        }
        else{
            return $dataset->getData();
        }
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

    protected function getDataset(DataSet $dataSet = null): Container {
        if($dataSet === null){
            return $this->dataset;
        }
        else{
            return $this->recieveDataset($this->inputsValues);
        }
    }
    public function getInputsValues(): Container {
        return $this->inputsValues;
    }

    public abstract function generate() : Container;
}

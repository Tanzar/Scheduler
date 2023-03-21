<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics\Engine\Generators;

use Custom\Statistics\Engine\Configs\Types\OutputForms as OutputForms;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Factory\InputsFactory as InputsFactory;
use Custom\Statistics\Engine\Configs\Elements\Datasets\Dataset as Dataset;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSets as ResultSets;
use Custom\Statistics\Engine\Configs\Exceptions\UndefinedInputValueException as UndefinedInputValueException;
use Tanweb\Container as Container;

/**
 * Description of StatsGenerator
 *
 * @author Tanzar
 */
abstract class StatsGenerator {
    private string $title;
    private InputsContainer $inputs;
    private ResultSets $resultSets;
    private OutputForms $outputForm;
    private Container $outputConfig;
    
    public function __construct(Container $config, Container $givenInputs) {
        $this->title = $config->get('title');
        $this->inputs = $this->formInputs($config, $givenInputs);
        $this->formResultSet($config);
        $form = $config->get('output_form');
        $this->outputForm = OutputForms::from($form);
        $outputCfg = $config->get('output_config');
        $this->outputConfig = new Container($outputCfg);
    }
    
    private function formInputs(Container $config, Container $givenInputs) : InputsContainer {
        $inputs = $config->get('inputs');
        $result = new InputsContainer();
        foreach ($inputs as $item) {
            $type = Inputs::from($item['type']);
            $value = $this->getInputValue($item, $givenInputs);
            $input = InputsFactory::create($type, $value);
            $result->add($input);
        }
        return $result;
    }
    
    private function getInputValue(array $input, Container $givenInputs) : string {
        if($input['value'] === ''){
            $value = '';
            foreach ($givenInputs->toArray() as $type => $val) {
                if($input['type'] === $type){
                    $value = $val;
                }
            }
            if($value === ''){
                throw new UndefinedInputValueException($input['type']);
            }
            else{
                return $value;
            }
        }
        else{
            return $input['value'];
        }
    }
    
    private function formResultSet(Container $config) : void {
        $this->resultSets = new ResultSets();
        $sets = $config->get('datasets');
        foreach ($sets as $item) {
            $config = new Container($item);
            $dataset = new Dataset($config, $this->inputs);
            $index = $item['index'];
            $resultSet = $dataset->formResultSet();
            $this->resultSets->add($index, $resultSet);
        }
    }
    
    public function generate() : Container {
        return $this->formToOutput($this->outputConfig);
    }
    
    protected function getTitle(): string {
        return $this->title;
    }

    protected function getInputs(): InputsContainer {
        return $this->inputs;
    }

    protected function getResultSets(): ResultSets {
        return $this->resultSets;
    }

    protected function getOutputForm(): OutputForms {
        return $this->outputForm;
    }

    protected function getOutputConfig(): Container {
        return $this->outputConfig;
    }

        
    protected abstract function formToOutput(Container $outputConfig) : Container;
}

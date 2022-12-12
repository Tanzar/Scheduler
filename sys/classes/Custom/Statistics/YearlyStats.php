<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Tanweb\Container as Container;
use Custom\Statistics\SingleStats as SingleStats;
use Custom\Statistics\Options\Type as Type;
use Custom\Statistics\Options\ResultForm as ResultForm;
use Custom\Statistics\Options\Input as Input;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of YearlyStats
 *
 * @author Tanzar
 */
class YearlyStats extends Statistics {
    private string $title;
    
    public function __construct(Container $data, Container $inputsValues) {
        parent::__construct($data, $inputsValues);
        $this->title = $data->get('name');
    }
    
    public function generate(): Container {
        $inputsValues = $this->getInputsValues();
        $start = (int) $inputsValues->get('yearStart');
        $end = (int) $inputsValues->get('yearEnd');
        $result = new Container();
        $results = array();
        for($year = $start; $year <= $end; $year++){
            $partial = $this->formStatsForYear($year);
            $results[] = $partial->toArray();
        }
        $json = $this->getJson();
        $type = $json->get('resultForm');
        $resultForm = ResultForm::from($type);
        if($resultForm === ResultForm::Table){
            $result->add('multiple_tables', 'type');
        }
        else{
            $result->add('multiple_plots', 'type');
        }
        $result->add($this->title, 'title');
        $result->add($results, 'data');
        return $result;
    }

    private function formStatsForYear(int $year) : Container {
        $inputsValues = $this->formInputs($year);
        $data = $this->formData();
        $singleStats = new SingleStats($data, $inputsValues);
        $result = $singleStats->generate();
        $result->add($year, 'title', true);
        return $result;
    }
    
    private function formInputs(int $year) : Container {
        $result = new Container();
        $inputValues = $this->getInputsValues();
        foreach ($inputValues->toArray() as $key => $value) {
            if($key !== 'yearStart' && $key !== 'yearEnd'){
                $result->add($value, $key);
            }
        }
        $result->add($year, 'year', true);
        return $result;
    }
    
    private function formData() : Container {
        $result = new Container();
        $result->add('noname', 'name');
        $json = $this->getJson();
        $inputs = $json->get('inputs');
        $newInputs = $this->filterInputs($inputs);
        $json->add($newInputs, 'inputs', true);
        $jsonString = json_encode($json->toArray(), JSON_UNESCAPED_UNICODE);
        $result->add($jsonString, 'json');
        $result->add(Type::Single->value, 'type');
        return $result;
    }
    
    private function filterInputs(array $inputs) : array {
        $result = new Container();
        foreach ($inputs as $key => $value) {
            $input = Input::from($value);
            if(!$result->contains(Input::Year->value) && 
                    ($input === Input::YearsRange || $input === Input::Year)){
                $result->add(Input::Year->value);
            }
            if($input !== Input::Year && $input !== Input::YearsRange && $input !== Input::MonthsRange){
                $result->add($input->value);
            }
        }
        return $result->toArray();
    }
}

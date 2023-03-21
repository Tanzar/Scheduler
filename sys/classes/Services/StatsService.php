<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\StatisticsDAO as StatisticsDAO;
use Custom\Statistics\Engine\Configs\Elements\Datasets\DataSources as DataSources;
use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\Operations as Operations;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Groups\Factory\GroupsFactory as GroupsFactory;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Custom\Statistics\Engine\Stats as Stats;
use Tanweb\Config\Template as Template;
use Tanweb\Container as Container;
use Services\Exceptions\StatsDatasetNotSetException as StatsDatasetNotSetException;
use Services\Exceptions\StatsOutputNotSetException as StatsOutputNotSetException;
/**
 * Description of StatsService
 *
 * @author Tanzar
 */
class StatsService {
    private StatisticsDAO $statistics;
    
    public function __construct() {
        $this->statistics = new StatisticsDAO();
    }
    
    public function getActive() : Container {
        $data = $this->statistics->getActive();
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $resultItem = $this->parseStatistic($item);
            $result->add($resultItem);
        }
        return $result;
    }
    
    public function getAll() : Container {
        $data = $this->statistics->getAll();
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $resultItem = $this->parseStatistic($item);
            $result->add($resultItem);
        }
        return $result;
    }
    
    public function getDatasets() : Container {
        $sources = DataSources::cases();
        return new Container($sources);
    }
    
    public function getOperations(DataSources $datasource) : Container {
        $operations = $datasource->getOpeartions();
        $result = array();
        foreach ($operations as $item){
            $result[] = $item->value;
        }
        return new Container($result);
    }
    
    public function getGroups(DataSources $datasource) : Container {
        $groups = Groups::cases();
        $result = new Container();
        foreach ($groups as $group){
            if($group === Groups::Activity || $group === Groups::ActivityType){
                if($datasource === DataSources::Inspections || $datasource === DataSources::Entries){
                    $result->add($group->value);
                }
            }
            elseif($group === Groups::Level){
                if($datasource === DataSources::Inspections){
                    $result->add($group->value);
                }
            }
            elseif($group === Groups::Instrument){
                if($datasource === DataSources::InstrumentUsages){
                    $result->add($group->value);
                }
            }
            else{
                $result->add($group->value);
            }
        }
        return $result;
    }
    
    public function getGroupsValues(Container $inputs, Container $groups) : Container {
        $result = new Container();
        $inputsContainer = new InputsContainer($inputs);
        foreach ($groups->toArray() as $item) {
            $type = Groups::from($item);
            $group = GroupsFactory::create($type);
            $values = $group->getOptions($inputsContainer);
            $result->add($values->toArray(), $item);
        }
        return $result;
    }
    
    public function getInputSettings(int $id) : Container {
        $data = $this->statistics->getById($id);
        $parsed = $this->parseStatistic($data->toArray());
        $preFiltered = $parsed['inputs'];
        $inputs = new Container();
        foreach ($preFiltered as $item) {
            if($item['value'] === ''){
                $inputs->add($item);
            }
        }
        $inputsContainer = new InputsContainer($inputs);
        $result = new Container();
        foreach ($inputsContainer->toArray() as $input) {
            $values = $this->getInputValues($input);
            $type = $input->getType();
            $key = $type->value;
            $result->add($values, $key);
        }
        return $result;
    }
    
    private function getInputValues(Input $input) : array {
        $values = $input->getOptions();
        return $values->toArray();
    }
    
    public function getTemplatesList() : Container {
        return Template::listTemplates('stats');
    }
    
    public function uploadTemplate(Container $file) : void {
        Template::uploadFile($file, 'stats');
    }
    
    public function generate(int $id, Container $inputs) : Container {
        $item = $this->statistics->getById($id);
        $parsed = $this->parseStatistic($item->toArray());
        $config = new Container($parsed);
        return Stats::generateOutput($config, $inputs);
    }
    
    public function generatePDF(int $id, Container $inputs) : void {
        $item = $this->statistics->getById($id);
        $parsed = $this->parseStatistic($item->toArray());
        $config = new Container($parsed);
        Stats::generateTablePDF($config, $inputs);
    }
    
    public function generateXLSX(int $id, Container $inputs) : void {
        $item = $this->statistics->getById($id);
        $parsed = $this->parseStatistic($item->toArray());
        $config = new Container($parsed);
        Stats::generateTableXLSX($config, $inputs);
    }
    
    public function saveStats(Container $stats) : int {
        if(!$stats->isValueSet('inputs')){
            $stats->add('[]', 'inputs');
        }
        else{
            $this->jsonToString('inputs', $stats);
        }
        if(!$stats->isValueSet('datasets')){
            throw new StatsDatasetNotSetException();
        }
        else{
            $this->jsonToString('datasets', $stats);
        }
        if(!$stats->isValueSet('output_config')){
            throw new StatsOutputNotSetException();
        }
        else{
            $this->jsonToString('output_config', $stats);
        }
        return $this->statistics->save($stats);
    }
    
    private function jsonToString(string $key, Container $item) : void {
        $json = $item->get($key);
        $text = json_encode( $json, JSON_UNESCAPED_UNICODE );
        $item->add($text, $key, true);
    }
    
    public function removeStats(int $id) : void {
        $this->statistics->remove($id);
    }
    
    private function parseStatistic(array $item) : array {
        return array(
            'id' => (int) $item['id'],
            'active' => (int) $item['active'],
            'title' => $item['title'],
            'sort_priority' => (int) $item['sort_priority'],
            'inputs' => json_decode($item['inputs'], true, 512, JSON_UNESCAPED_UNICODE),
            'datasets' => json_decode($item['datasets'], true, 512, JSON_UNESCAPED_UNICODE),
            'output_form' => $item['output_form'],
            'output_config' => json_decode($item['output_config'], true, 512, JSON_UNESCAPED_UNICODE),
        );
    }
}

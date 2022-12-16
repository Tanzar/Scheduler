<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\StatisticsDAO as StatisticsDAO;
use Custom\Statistics\Options\Type as Type;
use Custom\Statistics\Options\DataSet as DataSet;
use Custom\Statistics\Options\Input as Input;
use Custom\Statistics\Options\ResultForm as ResultForm;
use Custom\Statistics\Options\Group as Group;
use Custom\Statistics\Options\Method as Method;
use Custom\Statistics\Statistics as Statistics;
use Custom\Statistics\StatsPDF as StatsPDF;
use Custom\Statistics\StatsExcel as StatsExcel;
use Custom\Statistics\StatisticsFactory as StatisticsFactory;
use Tanweb\Config\Template as Template;
use Tanweb\Container as Container;
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
    
    public function getAllStatsWithoutForm() : Container {
        $stats = $this->statistics->getActiveWithoutForm();
        $results = new Container();
        foreach ($stats->toArray() as $item) {
            $stat = new Container($item);
            $parsed = array(
                'id' => $stat->get('id'),
                'name' => $stat->get('name'),
                'type' => $stat->get('type')
            );
            $jsonArray = json_decode(json_encode($stat->get('json')), true);
            $json = new Container($jsonArray);
            $parsed['form'] = $json->get('resultForm');
            $results->add($parsed);
        }
        return $results;
    }
    
    public function getAllFormStatistics() : Container {
        return $this->statistics->getActiveForm();
    }
    
    public function getActiveStats() : Container {
        $stats = $this->statistics->getActive();
        $results = new Container();
        foreach ($stats->toArray() as $item) {
            $stat = new Container($item);
            $parsed = array(
                'id' => $stat->get('id'),
                'name' => $stat->get('name'),
                'type' => $stat->get('type')
            );
            $jsonArray = json_decode(json_encode($stat->get('json')), true);
            $json = new Container($jsonArray);
            if($json->isValueSet('resultForm')){
                $parsed['form'] = $json->get('resultForm');
            }
            else{
                $parsed['form'] = 'XLSX';
            }
            $results->add($parsed);
        }
        return $results;
    }
    
    public function getTemplatesList() : Container {
        return Template::listTemplates('stats');
    }
    
    public function uploadTemplate(Container $file) : void {
        Template::uploadFile($file, 'stats');
    }
    
    public function getStatsSettingsStageOne() : Container {
        $result = new Container();
        $result->add($this->getStatsTypes(), 'types');
        $result->add($this->getDatasets(), 'datasets');
        $result->add($this->getInputs(), 'inputs');
        $result->add($this->getResultForms(), 'resultForms');
        return $result;
    }
    
    private function getStatsTypes() : array {
        $cases = Type::cases();
        $result = new Container();
        foreach ($cases as $item) {
            if(Type::from($item->value) != Type::Form){
                $result->add(array(
                    'title' => $item->value,
                    'value' => $item->value
                ));
            }
        }
        return $result->toArray();
    }
    
    private function getDatasets() : array {
        $cases = DataSet::cases();
        $result = new Container();
        foreach ($cases as $item) {
            $result->add(array(
                'title' => $item->value,
                'value' => $item->value
            ));
        }
        return $result->toArray();
    }
    
    private function getInputs() : array {
        $cases = Input::cases();
        $result = new Container();
        foreach ($cases as $item) {
            $result->add(array(
                'title' => $item->value,
                'value' => $item->value
            ));
        }
        return $result->toArray();
    }
    
    private function getResultForms() : array {
        $cases = ResultForm::cases();
        $result = new Container();
        foreach ($cases as $item) {
            $result->add(array(
                'title' => $item->value,
                'value' => $item->value
            ));
        }
        return $result->toArray();
    }
    
    public function getStatsSettingsStageTwo(Container $data) : Container {
        $result = new Container();
        $result->add($this->getGroups($data), 'groups');
        $result->add($this->getMethods($data), 'methods');
        return $result;
    }
    
    public function getGroups(Container $data = null) : array {
        if($data !== null){
            if($data->isValueSet('json')){
                $json = new Container($data->get('json'));
                $dataset = DataSet::from($json->get('dataset'));
            }
            else{
                $dataset = DataSet::from($data->get('dataset'));
            }
            $groups = Group::getGroupsForDataSet($dataset);
            return $groups->toArray();
        }
        else{
            return Group::cases();
        }
    }
    
    public function getGroupOptions(string $gorup) : Container {
        $grp = Group::from($gorup);
        return $grp->getOptions();
    }
    
    private function getMethods(Container $data) : array {
        $json = new Container($data->get('json'));
        $dataset = DataSet::from($json->get('dataset'));
        $methods = Method::getMethodsForDataSet($dataset);
        return $methods->toArray();
    }
    
    public function getInputSettings(int $id) : Container {
        $stats = $this->statistics->getById($id);
        $jsonText = $stats->get('json');
        $jsonArray = json_decode($jsonText, true);
        $json = new Container($jsonArray);
        $inputs = $json->get('inputs');
        $result = new Container();
        foreach($inputs as $item){
            $input = Input::from($item);
            $result->add($input->toJson()->toArray());
        }
        return $result;
    }
    
    public function getInputsOptions(Container $inputs) : Container {
        $result = new Container();
        foreach($inputs->toArray() as $item){
            $input = Input::from($item);
            $result->add($input->toJson()->toArray());
        }
        return $result;
    }
    
    public function saveStats(Container $data) : int {
        $this->checkInputs($data);
        $json = json_encode($data->get('json'), JSON_UNESCAPED_UNICODE);
        $data->add($json, 'json', true);
        return $this->statistics->save($data);
    }
    
    private function checkInputs(Container $data) : void {
        $type = Type::from($data->get('type'));
        if($type === Type::Monthly){
            $this->addInput($data, Input::MonthsRange);
        }
        if($type === Type::Yearly){
            $this->addInput($data, Input::YearsRange);
        }
    }
    
    private function addInput(Container $data, Input $input) : void {
        $json = new Container($data->get('json'));
        if(!$json->isValueSet('inputs')){
            $json->add(array(), 'inputs');
        }
        $inputs = new Container($json->get('inputs'));
        if(!$inputs->contains($input->value)){
            $inputs->add($input->value);
            $json->add($inputs->toArray(), 'inputs', true);
            $data->add($json->toArray(), 'json', true);
        }
    }
    
    public function saveFormStats(Container $data) : int {
        $json = json_encode($data->get('json'), JSON_UNESCAPED_UNICODE);
        $data->add(Type::Form->value, 'type');
        $data->add($json, 'json', true);
        return $this->statistics->save($data);
    }
    
    public function removeStats(int $id) : void {
        $this->statistics->remove($id);
    }
    
    public function generateStats(Container $data) : Container {
        $stats = StatisticsFactory::build($data);
        
        $result = $stats->generate();
        
        return $result;
    }
    
    public function generatePDF(Container $data) : void {
        $stats = StatisticsFactory::build($data);
        $result = $stats->generate();
        StatsPDF::formFile($result);
    }
    
    public function generateXlsx(Container $data) : void {
        $stats = StatisticsFactory::build($data);
        $result = $stats->generate();
        StatsExcel::formFile($result);
    }
}

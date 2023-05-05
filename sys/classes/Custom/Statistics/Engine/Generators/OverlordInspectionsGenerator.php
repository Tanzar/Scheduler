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
use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Config\Template as Template;
use Tanweb\Container as Container;

/**
 * Description of OverlordInspectionsGenerator
 *
 * @author Tanzar
 */
class OverlordInspectionsGenerator extends StatsGenerator {
    //put your code here
    protected function formToOutput(Container $outputConfig): Container {
        $resultSets = $this->getResultSets();
        $options = new Container();
        $groups = $outputConfig->get('groups');
        foreach ($groups as $group){
            $groupOptions = $this->getGroupValues($group);
            $options->add($groupOptions->toArray(), $group);
        }
        $resultSet = $resultSets->get($outputConfig->get('dataset'));
        $rows = $this->formRows($resultSet, $options);
        $t = $this->sortResults($rows);
        return $t;
    }

    private function formRows(ResultSet $resultSet, Container $groupOptions) : Container {
        $results = $resultSet->getValuesHigherThan(0);
        $result = new Container();
        foreach ($results->toArray() as $item) {
            $groupsCheck = $this->checkGroup($item, $groupOptions);
            if($groupsCheck){
                $row = $this->formRow($item, $groupOptions);
                $result->add($row);
            }
        }
        return $result;
    }
    
    private function checkGroup(array $item, Container $groupOptions) : bool {
        $groupsCheck = true;
        foreach ($groupOptions->toArray() as $key => $options){
            $group = Groups::from($key);
            if(!isset($item[$group->name])){
                $groupsCheck = false;
                break;
            }
        }
        return $groupsCheck;
    }

    private function formRow(array $item, Container $groupsOptions) : array {
        $result = array();
        foreach ($item as $key => $index) {
            $groupOptions = $this->getGroupOptionsByTypeName($groupsOptions, $key);
            $option = $this->getGroupOptionByValue($groupOptions, $index);
            if($item !== ''){
                $result[$key] = $option['title'];
            }
            else{
                $result[$key] = '';
            }
        }
        $result['value'] = $item['value'];
        return $result;
    }
    
    private function getGroupOptionsByTypeName(Container $groupsOptions, string $name) : array {
        foreach ($groupsOptions->toArray() as $key => $options) {
            $group = Groups::from($key);
            if($group->name === $name){
                return $options;
            }
        }
        return array();
    }
    
    private function getGroupOptionByValue(array $groupOptions, $value) : array {
        $i = 0;
        $item = '';
        while($item === '' && $i < count($groupOptions)){
            $grp = $groupOptions[$i];
            if($value == $grp['value']){
                $item = $grp;
            }
            $i++;
        }
        if($item === ''){
            $item = array('title' => 'Error', 'value' => 'error');
        }
        return $item;
    }
    
    private function getGroupValues(string $name) : Container {
        $type = Groups::from($name);
        $group = GroupsFactory::create($type);
        $inputs = $this->getInputs();
        return $group->getOptions($inputs);
    }
    
    private function sortResults(Container $rows) : Container {
        $arr = $rows->toArray();
        $cpy = array();
        foreach ($arr as $index => $item) {
            if($item['UserWithSUZUG'] === 'Error'){
                $item['UserWithSUZUG'] = 'Nie przypisany SZUG';
                $explod = [0 => 0, 1 => 'Nie przypisany inspektor'];
            }
            else{
                $explod = explode(' - ', $item['UserWithSUZUG']);
            }
            $row = array(
                'nr' => (int) $explod[0],
                'User' => $explod[1],
                'UserWithSUZUG' => $item['UserWithSUZUG'],
                'InspectableLocation' => $item['InspectableLocation'],
                'Activity' => $item['Activity'],
                'Level' => $item['Level'],
                'value' => $item['value']
            );
            $cpy[] = $row;
        }
        usort($cpy, function($a, $b) {
            return [$a['nr'], $a['InspectableLocation'], $a['Activity']] <=> [$b['nr'], $b['InspectableLocation'], $b['Activity']];
        });
        return new Container($cpy);
    }
}

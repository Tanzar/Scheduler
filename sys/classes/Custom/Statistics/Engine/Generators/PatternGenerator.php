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
 * Description of PatternGenerator
 *
 * @author Tanzar
 */
class PatternGenerator extends StatsGenerator {
    
    protected function formToOutput(Container $outputConfig): Container {
        $xlsx = new ExcelEditor();
        $filename = $outputConfig->get('filename');
        $path = Template::getLocalPathInnerDir($filename);
        $xlsx->openFile($path);
        $this->fillFile($xlsx, $outputConfig);
        $title = $this->getTitle();
        $xlsx->sendToBrowser($title);
        return new Container();
    }

    private function fillFile(ExcelEditor $xlsx, Container $outputConfig) : void {
        $sheet = $xlsx->getCurrentSheetName();
        $resultSets = $this->getResultSets();
        $cells = $outputConfig->get('cells');
        $writenCells = array();
        foreach ($cells as $cell){
            $index = $cell['dataset'];
            $resultSet = $resultSets->get($index);
            $address = '' . strtoupper($cell['col']) . $cell['row'];
            $val = $this->getCellVallue($cell, $resultSet);
            if(isset($writenCells[$address])){
                $value = $val + $writenCells[$address];
            }
            else{
                $value = $val;
                $writenCells[$address] = $value;
            }
            $xlsx->writeToCell($sheet, $address, $value);
        }
    }
    
    private function getCellVallue(array $cell, ResultSet $resultSet) : int {
        $groups = $cell['groups'];
        $groupsContainer = new GroupsContainer();
        foreach ($groups as $item) {
            $type = Groups::from($item['type']);
            $value = $item['value'];
            $group = GroupsFactory::create($type, $value);
            $groupsContainer->add($group);
        }
        return $resultSet->get($groupsContainer);
    }
}

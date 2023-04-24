<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Datasets;

use Custom\Statistics\Engine\Configs\Elements\Datasets\DataSources as DataSources;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Groups\Container\GroupsContainer as GroupsContainer;
use Custom\Statistics\Engine\Configs\Elements\Groups\Factory\GroupsFactory as GroupsFactory;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\Operations as Operations;
use Custom\Statistics\Engine\Configs\Elements\Operations\Abstracts\DataOperation as DataOperation;
use Custom\Statistics\Engine\Configs\Elements\Datasets\ResultSet as ResultSet;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\Database as Database;
use Tanweb\Container as Container;

/**
 * Description of Dataset
 *
 * @author Tanzar
 */
class Dataset {
    private DataSources $dataSource;
    private Container $data;
    private GroupsContainer $groups;
    private DataOperation $operation;
    
    public function __construct(Container $config, InputsContainer $inputs) {
        $dataSource = $config->get('dataset');
        $this->dataSource = DataSources::from($dataSource);
        $this->formGroups($config);
        $this->loadData($inputs);
        $operation = Operations::from($config->get('operation'));
        $this->operation = new DataOperation($operation);
    }
    
    private function formGroups(Container $config) : void {
        $groups = $config->get('groups');
        $this->groups = new GroupsContainer();
        foreach ($groups as $item) {
            $type = Groups::from($item);
            $group = GroupsFactory::create($type);
            $this->groups->add($group);
        }
    }
    
    private function loadData(InputsContainer $inputs) : void {
        $database = Database::getInstance('scheduler');
        $table = $this->dataSource->getViewName();
        $sql = new MysqlBuilder();
        $sql->select($table)->where('active', 1)->and();
        foreach ($inputs->toArray() as $index => $input) {
            $this->alterSQL($sql, $input);
            if($index <= $inputs->length() - 1){
                $sql->and();
            }
        }
        $this->data = $database->select($sql);
    }
    
    private function alterSQL(MysqlBuilder $sql, Input $input) : void {
        $input->alterDatasetSQL($sql, $this->dataSource);
    }
    
    public function formResultSet() : ResultSet {
        return $this->operation->formResult($this->data, $this->groups);
    }
}

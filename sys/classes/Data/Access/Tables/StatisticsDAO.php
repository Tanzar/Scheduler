<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Access\Tables;

use Data\Access\DataAccessObject as DAO;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;
use Custom\Statistics\Options\Type as Type;

/**
 * Description of StatisticsDAO
 *
 * @author Tanzar
 */
class StatisticsDAO extends DAO{
    
    public function __construct() {
        parent::__construct();
    }
    
    protected function setDatabaseIndex(): string {
        return 'scheduler';
    }

    protected function setDefaultTable(): string {
        return 'statistics';
    }
    
    public function geTActiveWithoutForm() : Container {
        $sql = new MysqlBuilder();
        $sql->select('statistics')->where('type', Type::Form->value, '!=');
        $stats = $this->select($sql);
        $this->parseJsons($stats);
        return $stats;
    }
    
    public function getActiveForm() : Container {
        $sql = new MysqlBuilder();
        $sql->select('statistics')->where('type', Type::Form->value);
        $stats = $this->select($sql);
        $this->parseJsons($stats);
        return $stats;
    }
    
    public function getActive() : Container {
        $sql = new MysqlBuilder();
        $sql->select('statistics')->where('active', 1);
        $stats = $this->select($sql);
        $this->parseJsons($stats);
        return $stats;
    }
    
    
    private function parseJsons(Container $stats) : void {
        foreach ($stats->toArray() as $key => $item){
            $stat = new Container($item);
            if($stat->isValueSet('json')){
                $json = json_decode($stat->get('json'));
                $stat->add($json, 'json', true);
                $stats->add($stat->toArray(), $key, true);
            }
        }
    }
}

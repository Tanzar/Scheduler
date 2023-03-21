<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts;

use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\InputFormHTML as InputFormHTML;
use Custom\Statistics\Engine\Configs\Elements\Datasets\DataSources as DataSources;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;

/**
 * Description of Input
 *
 * @author Tanzar
 */
abstract class Input {
    private Inputs $type;
    private string $value;
    
    protected function __construct(Inputs $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }
    
    public function getType(): Inputs {
        return $this->type;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function alterDatasetSQL(MysqlBuilder $sql, DataSources $datasource) : void {
        $this->alterDatasourceSQL($sql, $datasource, $this->value);
    }
    
    public abstract function getOptions() : Container;
    
    public abstract function getFormHTML() : InputFormHTML;
    
    protected abstract function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value) : void;
}

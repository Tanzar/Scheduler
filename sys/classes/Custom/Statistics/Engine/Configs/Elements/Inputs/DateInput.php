<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs;

use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\InputFormHTML as InputFormHTML;
use Custom\Statistics\Engine\Configs\Elements\Datasets\DataSources as DataSources;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of DateInput
 *
 * @author Tanzar
 */
class DateInput extends Input {
    
    public function __construct(DateTime $value) {
        parent::__construct(Inputs::Date, $value->format('Y-m-d'));
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Date;
    }

    public function getOptions(): Container {
        $result = new Container();
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        if($datasource === DataSources::Inspections || $datasource === DataSources::Entries){
            $sql->openBracket()->where('start', $value)->or()->where('end', $value)->closeBracket();
        }
        else{
            $sql->where('date', $value);
        }
    }

}

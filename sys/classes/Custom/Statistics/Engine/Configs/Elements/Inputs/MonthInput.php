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
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;

/**
 * Description of MonthInput
 *
 * @author Tanzar
 */
class MonthInput extends Input {
    
    public function __construct(int $value) {
        parent::__construct(Inputs::Month, $value);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $languages = Languages::getInstance();
        $months = $languages->get('months');
        $result = new Container();
        foreach ($months as $number => $name) {
            $option = array('title' => $name, 'value' => $number);
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        if($datasource === DataSources::Inspections || $datasource === DataSources::Entries){
            $sql->openBracket()->where('month(start)', $value)->or()
                    ->where('month(end)', $value)->closeBracket();
        }
        else{
            $sql->where('month(date)', $value);
        }
    }
}

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
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of ToYearInput
 *
 * @author Tanzar
 */
class ToYearInput extends Input {
    
    public function __construct(int $value) {
        parent::__construct(Inputs::ToYear, $value);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $appConfig = AppConfig::getInstance();
        $cfg = $appConfig->getAppConfig();
        $start = (int) $cfg->get('yearStart');
        $date = new DateTime();
        $end = (int) $date->format('Y');
        $result = new Container();
        for($year = $start; $year <= $end + 1; $year++){
            $option = array('title' => $year, 'value' => $year);
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        if($datasource === DataSources::Inspections || $datasource === DataSources::Entries){
            $sql->openBracket()->where('year(start)', $value, '<=')->or()
                    ->where('year(end)', $value, '<=')->closeBracket();
        }
        else{
            $sql->where('year(date)', $value, '<=');
        }
    }
}

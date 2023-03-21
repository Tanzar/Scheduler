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

/**
 * Description of UserTypeInput
 *
 * @author Tanzar
 */
class UserTypeInput extends Input {
    
    public function __construct(string $type) {
        parent::__construct(Inputs::UserType, $type);
    }
    
    public function getFormHTML(): InputFormHTML {
        return InputFormHTML::Select;
    }

    public function getOptions(): Container {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $inspectorTypes = $cfg->get('user_type_inspector');
        $result = new Container();
        foreach ($inspectorTypes as $type) {
            $option = array(
                'value' => $type,
                'title' => $type
            );
            $result->add($option);
        }
        return $result;
    }

    protected function alterDatasourceSQL(MysqlBuilder $sql, DataSources $datasource, $value): void {
        $sql->where('user_type', $value);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Data\Access\Views\SuzugUserDetailsView as SuzugUserDetailsView;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of SuzugGroup
 *
 * @author Tanzar
 */
class SuzugGroup  extends Group{
    
    public function __construct(string $value) {
        parent::__construct(Groups::NumberSUZUG, $value);
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $limit = (int) $cfg->get('suuzg_limit');
        for($i = 1; $i <= $limit; $i++){
            $results->add(array(
                'title' => $i,
                'value' => $i
            ));
        }
        return $results;
    }
    
    public function setValue(Container $row) : void {
        if($row->isValueSet('suzug_number')){
            $this->value = $row->get('suzug_number');
        }
    }

    public function getValueVariableName(): string {
        return 'suzug_number';
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;

/**
 * Description of ActivityTypeGroup
 *
 * @author Tanzar
 */
class ActivityGroupsGroup extends Group{
    
    public function __construct(string $value) {
        parent::__construct(Groups::ActivityType, $value);
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $groups = $cfg->get('activity_group');
        foreach($groups as $key => $item){
            $results->add(array(
                'title' => $item,
                'value' => $key
            ));
        }
        return $results;
    }
    
    public function setValue(Container $row) : void {
        if($row->isValueSet('activity_group')){
            $this->value = $row->get('activity_group');
        }
    }

    public function getValueVariableName(): string {
        return 'activity_group';
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Group as Group;
use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Tanweb\Container as Container;

/**
 * Description of LevelGroup
 *
 * @author Tanzar
 */
class LevelGroup  extends Group{
    
    public function __construct(int $value) {
        parent::__construct(Groups::Level, $value);
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $results->add(array('title' => 'Dół', 'value' => 1));
        $results->add(array('title' => 'Góra', 'value' => 0));
        return $results;
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('underground')){
            $this->value = $row->get('underground');
        }
    }

    public function getValueVariableName(): string {
        return 'underground';
    }

}

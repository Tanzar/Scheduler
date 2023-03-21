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
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of QuartersGroup
 *
 * @author Tanzar
 */
class QuartersGroup extends Group{
    
    public function __construct(int $value) {
        parent::__construct(Groups::Quarters, $value);
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $results->add(array('title' => 'I','value' => 1));
        $results->add(array('title' => 'II','value' => 2));
        $results->add(array('title' => 'III','value' => 3));
        $results->add(array('title' => 'IV','value' => 4));
        return $results;
    }
    
    public function setValue(Container $row) : void {
        if($row->isValueSet('date')){
            $text = $row->get('date');
            $date = new DateTime($text);
            $month = (int) $date->format("n");
            $quarter = ceil($month / 3);
            $this->value = (int) $quarter;
        }
    }

    public function getValueVariableName(): string {
        return 'date';
    }
}

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
 * Description of YearGroup
 *
 * @author Tanzar
 */
class YearGroup extends Group{
    private int $start;
    private int $end;
    
    public function __construct(int $value) {
        parent::__construct(Groups::Year, $value);
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $this->start = (int) $cfg->get('yearStart');
        $this->end = (int) date('Y') + 1;
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        for($year = $this->start; $year <= $this->end; $year++){
            if($this->isAcceptable($inputs, $year)){
                $results->add(array(
                    'title' => $year,
                    'value' => $year
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, int $year) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $year));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, int $year) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return $year === (int) $value;
            case Inputs::ToYear:
                return $year <= (int) $value;
            case Inputs::SinceYear:
                return $year >= (int) $value;
            case Inputs::Date:
                $date = new DateTime($value);
                return $year === (int) $date->format('Y');
            case Inputs::ToDate:
                $date = new DateTime($value);
                return $year <= (int) $date->format('Y');
            case Inputs::SinceDate:
                $date = new DateTime($value);
                return $year >= (int) $date->format('Y');
            default:
                return true;
        }
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('date')){
            $text = $row->get('date');
            $date = new DateTime($text);
            $this->value = $date->format('Y');
        }
    }

    public function getValueVariableName(): string {
        return 'date';
    }
}

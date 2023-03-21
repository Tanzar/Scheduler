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
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of MonthGroup
 *
 * @author Tanzar
 */
class MonthGroup extends Group{
    private DateTime $start;
    private array $months;
    
    public function __construct(int $value) {
        parent::__construct(Groups::Month, $value);
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $year = (int) $cfg->get('yearStart');
        $this->start = new DateTime($year . '-01-01 00:00:00');
        $languages = Languages::getInstance();
        $this->months = $languages->get('months');
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        for($month = 1; $month <= 12; $month++){
            if($this->isAcceptable($inputs, $month)){
                $results->add(array(
                    'title' => $this->months[$month],
                    'value' => $month
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, int $month) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $month));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, int $month) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Month:
                return $month === (int) $value;
            case Inputs::Date:
                $date = new DateTime($value);
                return $month === (int) $date->format('m');
            default:
                return true;
        }
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('date')){
            $text = $row->get('date');
            $date = new DateTime($text);
            $this->value = (int) $date->format('m');
        }
    }

    public function getValueVariableName(): string {
        return 'date';
    }
}

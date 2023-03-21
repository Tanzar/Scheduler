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
use Data\Access\Views\EquipmentDetailsView as EquipmentDetailsView;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of InstrumentGroup
 *
 * @author Tanzar
 */
class InstrumentGroup extends Group{
    private Container $instruments;
    
    public function __construct(int $value) {
        parent::__construct(Groups::Instrument, $value);
        $view = new EquipmentDetailsView();
        $this->instruments = $view->getAllMeasurementInstruments();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        foreach($this->instruments->toArray() as $item){
            $instrument = new Container($item);
            $id = (int) $instrument->get('id');
            if($this->isAcceptable($inputs, $instrument)){
                $results->add(array(
                    'title' => $instrument->get('name'),
                    'value' => $id
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, Container $instrument) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $instrument));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, Container $instrument) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return $this->isActiveInYear($instrument, (int) $value);
            case Inputs::ToYear:
                return $this->isActiveToYear($instrument, (int) $value);
            case Inputs::SinceYear:
                return $this->isActiveSinceYear($instrument, (int) $value);
            case Inputs::Date:
                return $this->isActiveAtDate($instrument, $value);
            case Inputs::ToDate:
                return $this->isActiveToDate($instrument, $value);
            case Inputs::SinceDate:
                return $this->isActiveSinceDate($instrument, $value);
            default:
                return true;
        }
    }
    
    private function isActiveInYear(Container $instrument, int $year) : bool {
        $start = new DateTime($instrument->get('start_date'));
        return $year >= (int) $start->format('Y');
    }
    
    private function isActiveToYear(Container $instrument, int $year) : bool {
        $start = new DateTime($instrument->get('start_date'));
        return $year <= (int) $start->format('Y');
    }
    
    private function isActiveSinceYear(Container $instrument, int $year) : bool {
        $start = new DateTime($instrument->get('start_date'));
        return $year >= (int) $start->format('Y');
    }
    
    private function isActiveAtDate(Container $instrument, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($instrument->get('start_date'));
        return $date >= $start;
    }
    
    private function isActiveToDate(Container $instrument, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($instrument->get('start_date'));
        return $date <= $start;
    }
    
    private function isActiveSinceDate(Container $instrument, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($instrument->get('start_date'));
        return $date >= $start;
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('id_equipment')){
            $this->value = $row->get('id_equipment');
        }
    }

    public function getValueVariableName(): string {
        return 'id_equipment';
    }
}

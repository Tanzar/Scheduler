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
use Data\Access\Views\LocationDetailsView as LocationDetailsView;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of LocationsGroup
 *
 * @author Tanzar
 */
class LocationsGroup extends Group{
    private Container $locations;
    
    public function __construct(int $value) {
        parent::__construct(Groups::Location, $value);
        $view = new LocationDetailsView();
        $this->locations = $view->getAll();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        foreach($this->locations->toArray() as $item){
            $location = new Container($item);
            $id = (int) $location->get('id');
            if($this->isAcceptable($inputs, $location)){
                $results->add(array(
                    'title' => $location->get('name'),
                    'value' => $id
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, Container $location) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $location));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, Container $location) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return $this->isActiveInYear($location, (int) $value);
            case Inputs::ToYear:
                return $this->isActiveToYear($location, (int) $value);
            case Inputs::SinceYear:
                return $this->isActiveSinceYear($location, (int) $value);
            case Inputs::Date:
                return $this->isActiveAtDate($location, $value);
            case Inputs::ToDate:
                return $this->isActiveToDate($location, $value);
            case Inputs::SinceDate:
                return $this->isActiveSinceDate($location, $value);
            case Inputs::Location:
                return $location->get('name') === $value;
            case Inputs::LocationGroup:
                return $location->get('loction_group') === $value;
            default:
                return true;
        }
    }
    
    private function isActiveInYear(Container $location, int $year) : bool {
        $start = new DateTime($location->get('active_from'));
        $end = new DateTime($location->get('active_to'));
        return $year >= (int) $start->format('Y') &&
                $year <= (int) $end->format('Y');
    }
    
    private function isActiveToYear(Container $location, int $year) : bool {
        $end = new DateTime($location->get('active_to'));
        return $year <= (int) $end->format('Y');
    }
    
    private function isActiveSinceYear(Container $location, int $year) : bool {
        $start = new DateTime($location->get('active_from'));
        return $year >= (int) $start->format('Y');
    }
    
    private function isActiveAtDate(Container $location, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($location->get('active_from'));
        $end = new DateTime($location->get('active_to'));
        return $date >= $start && $date <= $end;
    }
    
    private function isActiveToDate(Container $location, string $dateString) : bool {
        $date = new DateTime($dateString);
        $end = new DateTime($location->get('active_to'));
        return $date <= $end;
    }
    
    private function isActiveSinceDate(Container $location, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($location->get('active_form'));
        return $date <= $start;
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('id_location')){
            $this->value = $row->get('id_location');
        }
    }

    public function getValueVariableName(): string {
        return 'id_location';
    }
}

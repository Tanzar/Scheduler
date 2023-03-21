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
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of LocationsGroupsGroup
 *
 * @author Tanzar
 */
class LocationsGroupsGroup extends Group{
    private Container $locationsGroups;
    
    public function __construct(int $value) {
        parent::__construct(Groups::LocationGroup, $value);
        $dao = new LocationGroupDAO();
        $this->locationsGroups = $dao->getAll();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        foreach($this->locationsGroups->toArray() as $item){
            $locationGroup = new Container($item);
            $id = (int) $locationGroup->get('id');
            if($this->isAcceptable($inputs, $locationGroup)){
                $results->add(array(
                    'title' => $locationGroup->get('name'),
                    'value' => $id
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, Container $locationGroup) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $locationGroup));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, Container $locationGroup) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return $this->isActiveInYear($locationGroup, (int) $value);
            case Inputs::ToYear:
                return $this->isActiveToYear($locationGroup, (int) $value);
            case Inputs::SinceYear:
                return $this->isActiveSinceYear($locationGroup, (int) $value);
            case Inputs::Date:
                return $this->isActiveAtDate($locationGroup, $value);
            case Inputs::ToDate:
                return $this->isActiveToDate($locationGroup, $value);
            case Inputs::SinceDate:
                return $this->isActiveSinceDate($locationGroup, $value);
            case Inputs::LocationGroup:
                return $locationGroup->get('name') === $value;
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

    public function getColumn(): string {
        return 'id_location_group';
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('id_location_group')){
            $this->value = $row->get('id_location_group');
        }
    }

    public function getValueVariableName(): string {
        return 'id_location_group';
    }
}

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
use Data\Access\Views\UsersEmploymentPeriodsView as UsersEmploymentPeriodsView;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of UserGroup
 *
 * @author Tanzar
 */
class UserGroup extends Group{
    private Container $usersEmployments;
    
    public function __construct(string $value) {
        parent::__construct(Groups::User, $value);
        $view = new UsersEmploymentPeriodsView();
        $this->usersEmployments = $view->getActive();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $addedUsers = new Container();
        foreach($this->usersEmployments->toArray() as $item){
            $userEmployment = new Container($item);
            $id = (int) $userEmployment->get('id_user');
            if(!$addedUsers->contains($id) && $this->isAcceptable($inputs, $userEmployment)){
                $addedUsers->add($id);
                $results->add(array(
                    'title' => $userEmployment->get('surname') . ' ' . $userEmployment->get('name'),
                    'value' => $userEmployment->get('username')
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, Container $userEmployment) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $userEmployment));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, Container $userEmployment) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return $this->isEmployedInYear($userEmployment, (int) $value);
            case Inputs::ToYear:
                return $this->isEmployedToYear($userEmployment, (int) $value);
            case Inputs::SinceYear:
                return $this->isEmployedSinceYear($userEmployment, (int) $value);
            case Inputs::Date:
                return $this->isEmployedAtDate($userEmployment, $value);
            case Inputs::ToDate:
                return $this->isEmployedToDate($userEmployment, $value);
            case Inputs::SinceDate:
                return $this->isEmployedSinceDate($userEmployment, $value);
            case Inputs::User:
                return $userEmployment->get('username') === $value;
            case Inputs::Inspector:
                return $userEmployment->get('username') === $value;
            case Inputs::UserType:
                return $userEmployment->get('user_type') === $value;
            default:
                return true;
        }
    }
    
    private function isEmployedInYear(Container $userEmployment, int $year) : bool {
        $start = new DateTime($userEmployment->get('start'));
        $end = new DateTime($userEmployment->get('end'));
        return $year >= (int) $start->format('Y') &&
                $year <= (int) $end->format('Y');
    }
    
    private function isEmployedToYear(Container $userEmployment, int $year) : bool {
        $end = new DateTime($userEmployment->get('end'));
        return $year <= (int) $end->format('Y');
    }
    
    private function isEmployedSinceYear(Container $userEmployment, int $year) : bool {
        $start = new DateTime($userEmployment->get('start'));
        return $year >= (int) $start->format('Y');
    }
    
    private function isEmployedAtDate(Container $userEmployment, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($userEmployment->get('start'));
        $end = new DateTime($userEmployment->get('end'));
        return $date >= $start && $date <= $end;
    }
    
    private function isEmployedToDate(Container $userEmployment, string $dateString) : bool {
        $date = new DateTime($dateString);
        $end = new DateTime($userEmployment->get('end'));
        return $date >= $end;
    }
    
    private function isEmployedSinceDate(Container $userEmployment, string $dateString) : bool {
        $date = new DateTime($dateString);
        $start = new DateTime($userEmployment->get('start'));
        return $date >= $start;
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('username')){
            $this->value = $row->get('username');
        }
    }

    public function getValueVariableName(): string {
        return 'username';
    }
}

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
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of UserWithSuzugGroup
 *
 * @author Tanzar
 */
class UserWithSuzugGroup  extends Group{
    private Container $suzugUsers;
    
    public function __construct(string $value) {
        parent::__construct(Groups::UserWithSUZUG, $value);
        $view = new SuzugUserDetailsView();
        $this->suzugUsers = $view->getActiveOrderedByNumber();
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $addedUsers = new Container();
        foreach($this->suzugUsers->toArray() as $item){
            $suzugUsers = new Container($item);
            $id = (int) $suzugUsers->get('id_user');
            if(!$addedUsers->contains($id) && $this->isAcceptable($inputs, $suzugUsers)){
                $addedUsers->add($id);
                $results->add(array(
                    'title' => $suzugUsers->get('number') . ' - ' . $suzugUsers->get('surname') . ' ' . $suzugUsers->get('name'),
                    'value' => $suzugUsers->get('username')
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, Container $userSuzug) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $userSuzug));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, Container $suzugUser) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::Year:
                return (int) $suzugUser->get('year') === (int) $value;
            case Inputs::ToYear:
                return (int) $suzugUser->get('year') <= (int) $value;
            case Inputs::SinceYear:
                return (int) $suzugUser->get('year') >= (int) $value;
            case Inputs::Date:
                $date = new DateTime($value);
                return (int) $suzugUser->get('year') === (int) $date->format('Y');;
            case Inputs::ToDate:
                $date = new DateTime($value);
                return (int) $suzugUser->get('year') <= (int) $date->format('Y');;
            case Inputs::SinceDate:
                $date = new DateTime($value);
                return (int) $suzugUser->get('year') >= (int) $date->format('Y');;
            case Inputs::User:
                return $suzugUser->get('username') === $value;
            case Inputs::Inspector:
                return $suzugUser->get('username') === $value;
            default:
                return true;
        }
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

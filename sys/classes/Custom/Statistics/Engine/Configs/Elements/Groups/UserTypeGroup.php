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

/**
 * Description of UserTypeGroup
 *
 * @author Tanzar
 */
class UserTypeGroup extends Group{
    
    
    public function __construct(string $value) {
        parent::__construct(Groups::UserType, $value);
    }
    
    public function getOptions(InputsContainer $inputs): Container {
        $results = new Container();
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $userTypes = $cfg->get('user_type_inspector');
        foreach($userTypes as $type){
            if($this->isAcceptable($inputs, $type)){
                $results->add(array(
                    'title' => $type,
                    'value' => $type
                ));
            }
        }
        return $results;
    }
    
    private function isAcceptable(InputsContainer $inputs, string $type) : bool {
        $checks = new Container();
        foreach ($inputs->toArray() as $input) {
            $checks->add($this->check($input, $type));
        }
        return !$checks->contains(false);
    }
    
    private function check(Input $input, string $type) : bool {
        $inputType = $input->getType();
        $value = $input->getValue();
        switch ($inputType) {
            case Inputs::UserType:
                return $type === $value;
            default:
                return true;
        }
    }

    public function setValue(Container $row) : void {
        if($row->isValueSet('user_type')){
            $this->value = $row->get('user_type');
        }
    }

    public function getValueVariableName(): string {
        return 'user_type';
    }
}

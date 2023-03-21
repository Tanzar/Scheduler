<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts;

use Custom\Statistics\Engine\Configs\Elements\Groups\Abstracts\Groups as Groups;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Container\InputsContainer as InputsContainer;
use Tanweb\Container as Container;

/**
 * Description of Group
 *
 * @author Tanzar
 */
abstract class Group {
    private Groups $type;
    protected string $value;
    
    protected function __construct(Groups $type, string $value) {
        $this->type = $type;
        $this->value = $value;
    }
    
    public function getType(): Groups {
        return $this->type;
    }

    public function getValue(): string {
        return $this->value;
    }

    public abstract function getOptions(InputsContainer $inputs) : Container;
    
    public abstract function setValue(Container $row) : void;
    
    public abstract function getValueVariableName() : string;
}

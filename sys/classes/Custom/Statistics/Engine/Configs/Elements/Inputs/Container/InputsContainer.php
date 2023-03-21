<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Elements\Inputs\Container;

use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Input as Input;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Abstracts\Inputs as Inputs;
use Custom\Statistics\Engine\Configs\Elements\Inputs\Factory\InputsFactory as InputsFactory;
use Tanweb\Container as Container;

/**
 * Description of InputsContainer
 *
 * @author Tanzar
 */
class InputsContainer {
    private Container $data;
    
    public function __construct(Container $data = null) {
        $this->data = new Container();
        if($data !== null){
            foreach ($data->toArray() as $item) {
                $type = Inputs::from($item['type']);
                $input = InputsFactory::create($type, $item['value']);
                $this->add($input);
            }
        }
    }
    
    public function add(Input $input) : void {
        $this->data->add($input);
    }
    
    public function get(int $index) : Input {
        return $this->data->get($index);
    }
    
    public function length() : int {
        return $this->data->length();
    }
    
    public function toArray() : array {
        return $this->data->toArray();
    }
}

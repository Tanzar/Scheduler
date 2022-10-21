<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers;

use Tanweb\Container as Container;
use Custom\Parsers\ParserException as ParserException;

/**
 * Description of Parser
 *
 * @author Tanzar
 */
abstract class Parser {
    private Container $requiredVariables;
    private Container $optionalVariables;
    
    public function __construct() {
        $definedRequiredVariables = $this->defineRequiredVariables();
        $this->requiredVariables = new Container($definedRequiredVariables);
        $definedOptionalVariables = $this->defineOptionalVariables();
        $this->optionalVariables = new Container($definedOptionalVariables);
        
    }
    
    protected abstract function defineRequiredVariables() : array;
    
    protected abstract function defineOptionalVariables() : array;
    
    public function parse(Container $data) : Container {
        $result = $this->parseRequired($data);
        $this->parseOptional($data, $result);
        return $result;
    }
    
    private function parseRequired(Container $data) : Container {
        $result = new Container();
        foreach ($this->requiredVariables->toArray() as $variable) {
            $value = $this->getRequiredValue($data, $variable);
            $result->add($value, $variable);
        }
        return $result;
    }
    
    private function getRequiredValue(Container $data, string $variable) {
        if($data->isValueSet($variable)){
            return $data->get($variable);
        }
        else{
            throw new ParserException('value: ' . $variable . ' not found');
        }
    }
    
    private function parseOptional(Container $data, Container $result) : void {
        foreach ($this->optionalVariables->toArray() as $variable) {
            if($data->isValueSet($variable)){
                $value = $data->get($variable);
                $result->add($value, $variable);
            }
        }
    }
}

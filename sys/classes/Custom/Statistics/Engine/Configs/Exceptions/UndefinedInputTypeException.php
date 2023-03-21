<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of UndefinedInputTypeException
 *
 * @author Tanzar
 */
class UndefinedInputTypeException extends TanwebException {
    
    public function errorMessage(): string {
        return 'Stats error: Undefined input type - ' . $this->getMessage(); 
    }
}

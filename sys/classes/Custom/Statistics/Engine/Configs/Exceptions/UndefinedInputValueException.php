<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\Configs\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of UndefinedInputValueException
 *
 * @author Tanzar
 */
class UndefinedInputValueException extends TanwebException {
    
    public function errorMessage(): string {
        return 'Stats error: Undefined value for input: ' . $this->getMessage(); 
    }

}

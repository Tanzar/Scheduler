<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of IdColumnException
 *
 * @author Tanzar
 */
class IdColumnException extends TanwebException{
    
    public function errorMessage(): string {
        return 'Id column for ' . $this->getMessage() . ' is not properly set.';
    }
}

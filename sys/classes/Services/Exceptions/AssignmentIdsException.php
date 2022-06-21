<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of AssignmentIdsException
 *
 * @author Tanzar
 */
class AssignmentIdsException extends TanwebException{
    
    public function errorMessage(): string {
        return 'Assignment IDs error: ' . $this->getMessage();
    }

}

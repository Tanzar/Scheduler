<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of UserSelectException
 *
 * @author Tanzar
 */
class UserSelectException extends TanwebException{
    
    public function errorMessage(): string {
        return 'User select error: ' . $this->getMessage();
    }

}

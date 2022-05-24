<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of UserUpdateException
 *
 * @author Tanzar
 */
class UserUpdateException extends TanwebException{
    
    public function errorMessage(): string {
        return 'User Update error: ' . $this->getMessage();
    }

}

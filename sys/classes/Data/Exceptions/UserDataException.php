<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of UserDataException
 *
 * @author Tanzar
 */
class UserDataException extends TanwebException {
    
    public function errorMessage(): string {
        return 'UserData error: ' . $this->getMessage();
    }

}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of PrivilageException
 *
 * @author Tanzar
 */
class PrivilageException extends TanwebException{
    //put your code here
    public function errorMessage(): string {
        return 'Privilages error: ' . $this->getMessage();
    }

}

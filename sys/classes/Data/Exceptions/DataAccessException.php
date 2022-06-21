<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of DataAccessException
 *
 * @author Tanzar
 */
class DataAccessException extends TanwebException {
    //put your code here
    public function errorMessage(): string {
        return 'Data Access error: ' . $this->getMessage();
    }

}

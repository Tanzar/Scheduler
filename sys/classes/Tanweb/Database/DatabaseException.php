<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database;

use Tanweb\TanwebException as TanwebException;

/**
 * Exception for managing database errors
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class DatabaseException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Database error: ' . $this->getMessage();
    }
}

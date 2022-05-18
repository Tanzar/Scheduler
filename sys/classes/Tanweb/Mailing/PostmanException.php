<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Mailing;

use Tanweb\TanwebException as TanwebException;

/**
 * Exception for Postman
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class PostmanException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Postman error: ' . $this->getMessage();
    }
}

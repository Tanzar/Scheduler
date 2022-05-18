<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Security;

use Tanweb\TanwebException as TanwebException;

/**
 * Exception for Security
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class SecurityException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Security: ' . $this->getMessage();
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger;

use Tanweb\TanwebException as TanwebException;

/**
 * Exception for logger errors
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class LoggerException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Logger error: ' . $this->getMessage();
    }
}

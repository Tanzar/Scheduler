<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Request;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of RequestException
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class RequestException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Request error: ' . $this->getMessage();
    }
}

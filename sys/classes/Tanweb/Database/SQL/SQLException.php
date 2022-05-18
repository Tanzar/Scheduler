<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Database\SQL;

use Tanweb\TanwebException;

/**
 * Exception for builder errors
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class SQLException extends TanwebException{
    
    public function errorMessage() : string{
        return 'SQL Error: ' . $this->getMessage();
    }
}

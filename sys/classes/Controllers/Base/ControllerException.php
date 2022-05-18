<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers\Base;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of ControllerException
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class ControllerException extends TanwebException{
    
    public function errorMessage() : string{
        return $this->getMessage();
    }
}

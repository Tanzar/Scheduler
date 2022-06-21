<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of ScheduleEntryException
 *
 * @author Tanzar
 */
class ScheduleEntryException extends TanwebException{
    
    public function errorMessage(): string {
        return $this->getMessage();
    }

}

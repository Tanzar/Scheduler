<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Exceptions;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of ScheduleException
 *
 * @author Tanzar
 */
class ScheduleException extends TanwebException {
    
    public function errorMessage(): string {
        return 'Schedule error: ' . $this->getMessage();
    }

}

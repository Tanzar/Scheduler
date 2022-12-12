<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of LocationDatesException
 *
 * @author Tanzar
 */
class LocationDatesException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('start_earlier_than_end');
    }
}

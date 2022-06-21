<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;
/**
 * Description of OverlapingPeriodsException
 *
 * @author Tanzar
 */
class OverlapingPeriodsException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('overlaping_periods');
    }

}

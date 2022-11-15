<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;

/**
 * Description of YearNotUniqueException
 *
 * @author Tanzar
 */
class YearNotUniqueException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('document_number_for_year_already_exists');
    }

}

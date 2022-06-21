<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Data\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of UserNotFound
 *
 * @author Tanzar
 */
class NotFoundException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('not_found');
    }
}

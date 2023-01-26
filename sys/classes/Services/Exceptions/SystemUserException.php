<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of SystemUserException
 *
 * @author Tanzar
 */
class SystemUserException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('system_user_restriction');
    }
}

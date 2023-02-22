<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of EntryOutOfEmploymentException
 *
 * @author Tanzar
 */
class EntryOutOfEmploymentException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('entry_for_user_without_employment') . ': ' . $this->getMessage();
    }

}

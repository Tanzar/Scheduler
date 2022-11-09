<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\SystemErrors;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;
/**
 * Description of EntryException
 *
 * @author Tanzar
 */
class EntryException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return 'Scheduler System error: ' . $languages->get('entry_for_user_without_employment');
    }

}

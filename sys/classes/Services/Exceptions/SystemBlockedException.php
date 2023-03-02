<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;


/**
 * Description of SystemBlockedException
 *
 * @author Tanzar
 */
class SystemBlockedException extends TanwebException{
    //put your code here
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('cannot_change_selected_month');
    }

}

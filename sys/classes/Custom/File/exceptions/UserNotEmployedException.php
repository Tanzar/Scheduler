<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of UserNotEmployedException
 *
 * @author Tanzar
 */
class UserNotEmployedException extends TanwebException{
    //put your code here
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('user_not_employed');
    }

}

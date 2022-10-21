<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of ParserException
 *
 * @author Tanzar
 */
class ParserException extends TanwebException{
    //put your code here
    public function errorMessage(): string {
        return 'Parser error: ' . $this->getMessage();
    }

}

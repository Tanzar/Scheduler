<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Calculation\exceptions;

use Tanweb\TanwebException as TanwebException;
/**
 * Description of OvertimeException
 *
 * @author Tanzar
 */
class OvertimeException extends TanwebException{
    //put your code here
    public function errorMessage(): string {
        return 'Overtime calculation error: ' . $this->getMessage();
    }

}

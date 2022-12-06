<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Statistics\Exceptions;

use Tanweb\TanwebException as TanwebException;
/**
 * Description of UnsupportedStatisticsTypeException
 *
 * @author Tanzar
 */
class UnsupportedStatisticsTypeException  extends TanwebException{
    
    public function errorMessage(): string {
        return 'Unsupported statistics type: ' . $this->getMessage();
    }

}

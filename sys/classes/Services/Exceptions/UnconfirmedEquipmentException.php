<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services\Exceptions;

use Tanweb\TanwebException as TanwebException;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of UnconfirmedEquipmentException
 *
 * @author Tanzar
 */
class UnconfirmedEquipmentException extends TanwebException{
    
    public function errorMessage(): string {
        $languages = Languages::getInstance();
        return $languages->get('unconfirmed_equipment_assignment');
    }

}

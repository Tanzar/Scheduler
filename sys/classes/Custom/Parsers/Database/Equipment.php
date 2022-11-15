<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Equipment
 *
 * @author Tanzar
 */
class Equipment extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array(
            'id', 'active', 'calibration'
        );
    }

    protected function defineRequiredVariables(): array {
        return array(
            'name', 'document', 'start_date', 'inventory_number', 'price', 
            'remarks', 'id_user', 'id_equipment_type', 'state'
        );
    }

}

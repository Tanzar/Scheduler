<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Qualification
 *
 * @author Tanzar
 */
class Qualification extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('number', 'date', 'position', 'facility', 'specialization', 
            'id_person', 'id_oug_offices', 'id_facility_type', 'id_supervision_level');
    }

}

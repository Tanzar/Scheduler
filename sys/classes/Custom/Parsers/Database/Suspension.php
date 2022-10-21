<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Suspension
 *
 * @author Tanzar
 */
class Suspension extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id' , 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('date', 'shift', 'region', 'description',
            'correction_date', 'correction_shift', 'remarks',
            'external_company', 'company_name', 'id_suspension_type', 
            'id_suspension_object', 'id_suspension_reason', 'id_document_user');
    }

}

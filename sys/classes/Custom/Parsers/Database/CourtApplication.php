<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of CourtApplication
 *
 * @author Tanzar
 */
class CourtApplication extends Parser {
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('position', 'value', 'date', 'accusation', 
            'external_company', 'company_name', 'remarks', 'id_document_user',
            'id_position_groups');
    }

}

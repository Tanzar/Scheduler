<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of InstrumentUsage
 *
 * @author Tanzar
 */
class InstrumentUsage extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('date', 'remarks', 'recommendation_decision', 
            'id_document_user', 'id_equipment');
    }

}

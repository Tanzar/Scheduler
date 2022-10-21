<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Decision
 *
 * @author Tanzar
 */
class Decision extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('date', 'description', 'remarks', 'id_decision_law',
            'id_document_user');
    }

}

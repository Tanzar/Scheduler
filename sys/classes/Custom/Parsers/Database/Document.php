<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Document
 *
 * @author Tanzar
 */
class Document extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('start', 'end', 'number', 'description', 'id_location');
    }

}

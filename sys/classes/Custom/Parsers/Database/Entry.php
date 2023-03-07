<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Entry
 *
 * @author Tanzar
 */
class Entry extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active', 'underground', 'description');
    }

    protected function defineRequiredVariables(): array {
        return array('start', 'end', 'id_user', 'id_activity', 'id_location');
    }

}

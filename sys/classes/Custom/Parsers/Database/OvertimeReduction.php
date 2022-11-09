<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of OvertimeReduction
 *
 * @author Tanzar
 */
class OvertimeReduction extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id', 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('time', 'date', 'id_user');
    }

}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of SuzugUser
 *
 * @author Tanzar
 */
class SuzugUser extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id' , 'active');
    }

    protected function defineRequiredVariables(): array {
        return array('year', 'number',  'id_user');
    }
}

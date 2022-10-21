<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Ticket
 *
 * @author Tanzar
 */
class Ticket extends Parser{
    //put your code here
    protected function defineOptionalVariables(): array {
        return array('id');
    }

    protected function defineRequiredVariables(): array {
        return array('id_position_groups', 'id_ticket_law', 'id_document_user',
            'number', 'position', 'value', 'date', 'violated_rules',
            'external_company', 'company_name', 'remarks');
    }

}

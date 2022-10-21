<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Parsers\Database;

use Custom\Parsers\Parser as Parser;

/**
 * Description of Article
 *
 * @author Tanzar
 */
class Article extends Parser{
    
    protected function defineOptionalVariables(): array {
        return array('id');
    }

    protected function defineRequiredVariables(): array {
        return array('position', 'applicant', 'application_number',
            'application_date', 'date', 'external_company', 'company_name',
            'remarks', 'id_art_41_form', 'id_position_groups', 'id_document_user');
    }

}

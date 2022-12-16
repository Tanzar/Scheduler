<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\TanwebException as TanwebException;

/**
 * Description of TemplateException
 *
 * @author Tanzar
 */
class TemplateException extends TanwebException{
    
    public function errorMessage(): string {
        return 'Template error: ' . $this->getMessage();
    }

}

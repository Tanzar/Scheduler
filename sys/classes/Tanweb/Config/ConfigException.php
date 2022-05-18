<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Config;

use Tanweb\TanwebException as TanwebException;

/**
 * Seception throws when there is error in variables form config.ini file
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class ConfigException extends TanwebException{
    
    public function errorMessage() : string{
        return 'Config.ini error: ' . $this->getMessage();
    }
}

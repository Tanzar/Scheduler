<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for security informs about logins and logouts and when access was denied,
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class SecurityEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Security';
    }

}

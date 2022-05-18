<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for reporting server errors (throwables that don't extend TanwebException)
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class ErrorEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Error';
    }

}

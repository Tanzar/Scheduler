<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for reporting Insert SQL Queries
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class InsertEntry extends LogEntry{
    
    protected function setType(): string {
        return 'insert';
    }

}

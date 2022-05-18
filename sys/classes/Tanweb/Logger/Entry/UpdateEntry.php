<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for reporting Update SQL Queries
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class UpdateEntry extends LogEntry{
    
    protected function setType(): string {
        return 'update';
    }

}

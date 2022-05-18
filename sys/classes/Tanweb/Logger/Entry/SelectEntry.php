<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;
/**
 * Entry for recording Select SQL Queries
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class SelectEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Select';
    }

}

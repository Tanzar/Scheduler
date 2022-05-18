<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for tracking what pages user requests
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class AccessEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Access';
    }

}

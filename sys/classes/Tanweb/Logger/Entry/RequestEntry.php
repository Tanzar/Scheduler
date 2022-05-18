<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Tanweb\Logger\Entry;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Entry for  calling controllers requests and trackig page requests
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class RequestEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Request';
    }

}

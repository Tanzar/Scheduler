<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Cleaner;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Description of CleanerEntry
 *
 * @author Tanzar
 */
class CleanerEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Clean';
    }

}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\DBFixer;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Description of FixerEntry
 *
 * @author Tanzar
 */
class FixerEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Fixer';
    }

}

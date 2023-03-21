<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Logs;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Description of PrintsLog
 *
 * @author Tanzar
 */
class PrintsLog extends LogEntry {
    
    protected function setType(): string {
        return 'Prints';
    }

}

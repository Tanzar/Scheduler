<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Logs;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Description of StatsLog
 *
 * @author Tanzar
 */
class StatsLog extends LogEntry {
    
    protected function setType(): string {
        return 'Stats';
    }

}

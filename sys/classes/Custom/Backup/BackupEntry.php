<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Backup;

use Tanweb\Logger\Entry\LogEntry as LogEntry;

/**
 * Description of BackupEntry
 *
 * @author Tanzar
 */
class BackupEntry extends LogEntry{
    
    protected function setType(): string {
        return 'Backup';
    }

}

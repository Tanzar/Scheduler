<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\DBFixer;

/**
 * Description of FixerReport
 *
 * @author Tanzar
 */
class FixerReport {
    private int $disabled = 0;
    private int $removed = 0;
    private int $datesChanged = 0;
    private int $enabled = 0;
    
    public function getDisabled(): int {
        return $this->disabled;
    }

    public function getRemoved(): int {
        return $this->removed;
    }

    public function getDatesChanged(): int {
        return $this->datesChanged;
    }

    public function getEnabled(): int {
        return $this->enabled;
    }
    
    public function addDisabled() : void {
        $this->disabled++;
    }
    
    public function addRemoved() : void {
        $this->removed++;
    }
    
    public function addDatesChanged() : void {
        $this->datesChanged++;
    }
    
    public function addEnabled() : void {
        $this->enabled++;
    }
    
    public function toString() : string {
        $text = '';
        $text .= 'Disabled: ' . $this->disabled . ', ';
        $text .= 'Enabled: ' . $this->enabled . ', ';
        $text .= 'Dates changed: ' . $this->datesChanged . ', ';
        $text .= 'Removed: ' . $this->removed;
        return $text;
    }
}

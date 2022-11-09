<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\File\Tools\Timesheets;

use Custom\File\Tools\Timesheets\General as General;
use Custom\File\Tools\Timesheets\Rows as Rows;
use Tanweb\Container as Container;
use DateTime;

/**
 * Description of Settings
 *
 * @author Tanzar
 */
class Settings {
    private General $general;
    private Rows $rows;
    
    public function __construct(int $month, int $year, string $username) {
        $this->general = new General($month, $year, $username);
        $this->rows = new Rows($month, $year, $username);
    }
    
    public function getMonth(): int {
        return $this->general->getMonth();
    }

    public function getYear(): int {
        return $this->general->getYear();
    }

    public function getUsername(): string {
        return $this->general->getUsername();
    }

    public function getFullUserName(): string {
        return $this->general->getFullUserName();
    }

    public function getStandardFullTime(): int {
        return $this->general->getStandardFullTime();
    }

    public function getOrganization(): string {
        return $this->general->getOrganization();
    }

    public function getPreviousOvertime(): int {
        return $this->rows->calculatePreviousOvertime();
    }
    
    public function getRow(string $row) : array {
        return $this->rows->getRow($row);
    }
    
    public function isDayOff(DateTime $date) : bool {
        return $this->rows->isDayOff($date);
    }
    
    public function getUniqueHoursSets() : Container {
        return $this->rows->getUniqueHoursSets();
    }
    
    public function countWorkDays() : int {
        return $this->rows->countWorkDays();
    }
    
    public function summarizeRow(string $row) : int {
        return $this->rows->summarizeRow($row);
    }
    
    public function getCurrentWorktime() : int {
        return $this->rows->calculateCurrentWorktime();
    }
    
    public function getPassingOvertime() : int {
        return $this->rows->calculatePassingOvertime();
    }
}

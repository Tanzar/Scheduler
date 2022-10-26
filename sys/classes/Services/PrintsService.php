<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Custom\File\AttendanceList as AttendanceList;

/**
 * Description of PrintsService
 *
 * @author Tanzar
 */
class PrintsService {
    
    public function generateAttendanceList(int $month, int $year) : void {
        AttendanceList::generate($month, $year);
    }
}

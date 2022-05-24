<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;

/**
 * Description of ScheduleUser
 *
 * @author Tanzar
 */
class ScheduleUser extends Controller{
    
    public function getEntries(){
        $data = $this->getRequestData();
        $start = $data->getValue('startDate');
        $end = $data->getValue('endDate');
        
    }
}

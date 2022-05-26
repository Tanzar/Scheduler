<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\ActivityService as ActivityService;
use Services\ScheduleService as ScheduleService;
use Tanweb\Container as Container;


/**
 * Description of AdminPanelSchedule
 *
 * @author Tanzar
 */
class AdminPanelSchedule extends Controller{
    private ActivityService $activity;
    private ScheduleService $schedule;
    
    
    public function __construct() {
        $this->activity = new ActivityService();
        $this->schedule = new ScheduleService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    
}

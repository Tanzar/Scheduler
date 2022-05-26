<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\ActivityDataAccess as ActivityDataAccess;
use  Tanweb\Container as Container;

/**
 * Description of ActivityService
 *
 * @author Tanzar
 */
class ActivityService {
    private ActivityDataAccess $activity;
    
    public function __construct() {
        $this->activity = new ActivityDataAccess();
    }
    
    public function getActivities() : Container{
        return $this->activity->getActivities();
    }
    
    public function getActivityGroups() : Container{
        return $this->activity->getActivityGroups();
    }
    
    public function addActivity(Container $data) : int {
        return $this->activity->newActivity($data);
    }
    
    public function addActivityGroup(string $name) : int {
        $data = new Container();
        $data->add($name, 'name');
        return $this->activity->newActivityGroup($data);
    }
}

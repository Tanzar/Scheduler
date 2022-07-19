<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\PositionGroupsDAO as PositionGroupsDAO;
use Tanweb\Container as Container;

/**
 * Description of PositionGroupsService
 *
 * @author Tanzar
 */
class PositionGroupsService {
    private PositionGroupsDAO $positionGroups;
    
    public function __construct() {
        $this->positionGroups = new PositionGroupsDAO();
    }
    
    public function getAll() : Container {
        return $this->positionGroups->getAll();
    }
    
    public function getActive() : Container {
        return $this->positionGroups->getActive();
    }
    
    public function save(Container $data) : int {
        return $this->positionGroups->save($data);
    }
    
    public function changeStatus(int $id) : void {
        $group = $this->positionGroups->getById($id);
        $active = $group->get('active');
        if($active){
            $this->positionGroups->disable($id);
        }
        else{
            $this->positionGroups->enable($id);
        }
    }
}

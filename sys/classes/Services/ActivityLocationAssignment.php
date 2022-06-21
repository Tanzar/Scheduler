<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\ActivityLocationTypeDAO as ActivityLocationTypeDAO;
use Services\Exceptions\AssignmentIdsException as AssignmentIdsException;
use Tanweb\Container as Container;

/**
 * Description of ActivityLocationAssignment
 *
 * @author Tanzar
 */
class ActivityLocationAssignment {
    private ActivityLocationTypeDAO $assigner;
    
    public function __construct() {
        $this->assigner = new ActivityLocationTypeDAO();
    }
    
    public function getAll() : Container {
        return $this->assigner->getAll();
    }
    
    public function assign(int $idActivity, int $idLocationType) : int {
        $relations = $this->assigner->getByIds($idActivity, $idLocationType);
        $count = $relations->length();
        if($count === 0){
            $item = new Container();
            $item->add($idActivity, 'id_activity');
            $item->add($idLocationType, 'id_location_type');
            return $this->assigner->save($item);
        }
        if($count > 1){
            throw new AssignmentIdsException('multiple versions of same relation'
                    . 'for id_activity = ' . $idActivity . ' and id_location_type = '
                    . $idLocationType . ' only one allowed, remove excess.');
        }
        if($count === 1){
            $relation = new Container($relations->get(0));
            $id = (int) $relation->get('id');
            $this->assigner->enable($id);
            return $id;
        }
        return -1;
    }
    
    public function changeStatus(int $id){
        $assignment = $this->assigner->getByID($id);
        $active = $assignment->get('active');
        if($active){
            $this->assigner->disable($id);
        }
        else{
            $this->assigner->enable($id);
        }
    }
    
    public function delete(int $id){
        $this->assigner->remove($id);
    }
    
    
}

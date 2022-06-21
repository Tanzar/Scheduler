<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\LocationDAO as LocationDAO;
use Data\Access\Tables\LocationTypeDAO as LocationTypeDAO;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Tanweb\Container as Container;

/**
 * Description of LocationService
 *
 * @author Tanzar
 */
class LocationService {
    private LocationDAO $location;
    private LocationTypeDAO $type;
    private LocationGroupDAO $group;
    
    public function __construct() {
        $this->location = new LocationDAO();
        $this->type = new LocationTypeDAO();
        $this->group = new LocationGroupDAO();
    }
    
    public function getAllLocations() : Container {
        return $this->location->getAll();
    }
    
    public function getActiveLocations() : Container {
        return $this->location->getActive();
    }
    
    public function getLocationsByTypeID(int $idType) : Container {
        return $this->location->getByTypeId($idType);
    }
    
    public function getAllLocationTypes() : Container {
        return $this->type->getAll();
    }
    
    public function getActiveLocationTypes() : Container {
        return $this->type->getActive();
    }
    
    public function getAllLocationGroups() : Container {
        return $this->group->getAll();
    }
    
    public function getActiveLocationGroups() : Container {
        return $this->group->getActive();
    }
    
    public function saveLocation(Container $data) : int {
        return $this->location->save($data);
    }
    
    public function saveLocationType(Container $data) : int {
        return $this->type->save($data);
    }
    
    public function saveLocationGroup(Container $data) : int {
        return $this->group->save($data);
    }
    
    public function changeLocationStatus(int $id) : void {
        $location = $this->location->getByID($id);
        $active = $location->get('active');
        if($active){
            $this->location->disable($id);
        }
        else{
            $this->location->enable($id);
        }
    }
    
    public function changeLocationTypeStatus(int $id) : void {
        $type = $this->type->getByID($id);
        $active = $type->get('active');
        if($active){
            $this->type->disable($id);
        }
        else{
            $this->type->enable($id);
        }
    }
    
    public function changeLocationGroupStatus(int $id) : void {
        $group = $this->group->getByID($id);
        $active = $group->get('active');
        if($active){
            $this->group->disable($id);
        }
        else{
            $this->group->enable($id);
        }
    }
}

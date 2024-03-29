<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\LocationDAO as LocationDAO;
use Data\Access\Tables\LocationTypeDAO as LocationTypeDAO;
use Data\Access\Tables\LocationGroupDAO as LocationGroupDAO;
use Data\Access\Views\LocationDetailsView as LocationDetailsView;
use Tanweb\Container as Container;
use Services\Exceptions\LocationDatesException as LocationDatesException;
use DateTime;

/**
 * Description of LocationService
 *
 * @author Tanzar
 */
class LocationService {
    private LocationDAO $location;
    private LocationTypeDAO $locationType;
    private LocationGroupDAO $locationGroup;
    private LocationDetailsView $locationDetails;
    
    public function __construct() {
        $this->location = new LocationDAO();
        $this->locationType = new LocationTypeDAO();
        $this->locationGroup = new LocationGroupDAO();
        $this->locationDetails = new LocationDetailsView();
    }
    
    public function getAllLocations() : Container {
        return $this->location->getAll();
    }
    
    public function getActiveLocations() : Container {
        return $this->location->getActive();
    }
    
    public function getLocationsByGroupId(int $id) : Container {
        return $this->locationDetails->getByIdLocationGroup($id);
    }
    
    public function getLocationsByTypeID(int $idType) : Container {
        return $this->locationDetails->getByIdLocationType($idType);
    }
    
    public function getActiveLocationsByTypeID(int $idType) : Container {
        return $this->locationDetails->getActiveByIdLocationType($idType);
    }
    
    public function getAllLocationTypes() : Container {
        return $this->locationType->getAll();
    }
    
    public function getActiveLocationTypes() : Container {
        return $this->locationType->getActive();
    }
    
    public function getActiveInspectableLocationTypes() : Container {
        return $this->locationType->getActiveByInspection(true);
    }
    
    public function getAllLocationGroups() : Container {
        return $this->locationGroup->getAll();
    }
    
    public function getActiveLocationGroups() : Container {
        return $this->locationGroup->getActive();
    }
    
    public function saveLocation(Container $data) : int {
        $start = $data->get('active_from');
        $end = $data->get('active_to');
        if($start >= $end){
            throw new LocationDatesException();
        }
        return $this->location->save($data);
    }
    
    public function saveLocationType(Container $data) : int {
        return $this->locationType->save($data);
    }
    
    public function saveLocationGroup(Container $data) : int {
        $start = $data->get('active_from');
        $end = $data->get('active_to');
        if($start >= $end){
            throw new LocationDatesException();
        }
        return $this->locationGroup->save($data);
    }
    
    public function saveLocationAsIllegal(string $name) : int {
        $item = new Container();
        $item->add($name, 'name');
        $locationTypeId = $this->getIllegalLocationTypeId();
        $item->add($locationTypeId, 'id_location_type');
        $groupId = $this->getTemporaryGroupId();
        $date = new DateTime(date(('Y-m') . '-1'));
        $item->add($date->format('Y-m-d'), 'active_from');
        $date->modify('+100 years');
        $item->add($date->format('Y-m-d'), 'active_to');
        $item->add($groupId, 'id_location_group');
        return $this->location->save($item);
    }
    
    private function getIllegalLocationTypeId() : int {
        $groups = $this->locationType->getByName('Illegal Mining');
        if($groups->isEmpty()){
            $item = new Container();
            $item->add('Illegal Mining', 'name');
            $item->add(0, 'active');
            $item->add('IM', 'short');
            $item->add(1, 'inspection');
            return $this->locationType->save($item);
        }
        else{
            $item = $groups->get(0);
            $group = new Container($item);
            $id = (int) $group->get('id');
            return $id;
        }
    }
    
    private function getTemporaryGroupId() : int {
        $groups = $this->locationGroup->getByName('tmp');
        if($groups->isEmpty()){
            $item = new Container();
            $item->add('tmp', 'name');
            $item->add(0, 'active');
            $date = new DateTime(date(('Y-m') . '-1'));
            $item->add($date->format('Y-m-d'), 'active_from');
            $date->modify('+100 years');
            $item->add($date->format('Y-m-d'), 'active_to');
            return $this->locationGroup->save($item);
        }
        else{
            $item = $groups->get(0);
            $group = new Container($item);
            $id = (int) $group->get('id');
            return $id;
        }
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
        $type = $this->locationType->getByID($id);
        $active = $type->get('active');
        if($active){
            $this->locationType->disable($id);
        }
        else{
            $this->locationType->enable($id);
        }
    }
    
    public function changeLocationGroupStatus(int $id) : void {
        $group = $this->locationGroup->getByID($id);
        $active = $group->get('active');
        if($active){
            $this->locationGroup->disable($id);
        }
        else{
            $this->locationGroup->enable($id);
        }
    }
}

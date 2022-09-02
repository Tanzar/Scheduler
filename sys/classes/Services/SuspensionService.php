<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\SuspensionSuzugGroupDAO as SuspensionSuzugGroupDAO;
use Data\Access\Tables\SuspensionSuzugTypeDAO as SuspensionSuzugTypeDAO;
use Data\Access\Tables\SuspensionReasonDAO as SuspensionReasonDAO;
use Data\Access\Views\SuspensionDetailsView as SuspensionDetailsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;


/**
 * Description of SuspensionService
 *
 * @author Tanzar
 */
class SuspensionService {
    private SuspensionSuzugGroupDAO $suspensionSuzugGroup;
    private SuspensionSuzugTypeDAO $suspensionSuzugType;
    private SuspensionReasonDAO $suspensionReason;
    private SuspensionDetailsView $suspensionDetails;
    
    public function __construct() {
        $this->suspensionSuzugGroup = new SuspensionSuzugGroupDAO();
        $this->suspensionSuzugType = new SuspensionSuzugTypeDAO();
        $this->suspensionReason = new SuspensionReasonDAO();
        $this->suspensionDetails = new SuspensionDetailsView();
    }
    
    public function getAllSuzugTypes() : Container {
        return $this->suspensionSuzugType->getAll();
    }
    
    public function getAlSuzuglTypesByGroupId(int $id) : Container {
        return $this->suspensionSuzugType->getByGroupId($id);
    }
    
    public function getActiveSuzugTypesByGroupId(int $id) : Container {
        return $this->suspensionSuzugType->getActiveByGroupId($id);
    }
    
    public function getAllSuzugGroups() : Container {
        return $this->suspensionSuzugGroup->getAll();
    }
    
    public function getActiveSuzugGroups() : Container {
        return $this->suspensionSuzugGroup->getActive();
    }
    
    public function getAllReasons() : Container {
        return $this->suspensionReason->getAll();
    }
    
    public function getCurrentUserSuspensions(int $idDocument) : Container {
        $username = Session::getUsername();
        return $this->suspensionDetails->getActiveByUsernameAndIdDocument($username, $idDocument);
    }
    
    public function saveSuzugType(Container $data) : int{
        return $this->suspensionSuzugType->save($data);
    }
    
    public function saveSuzugGroup(Container $data) : int{
        return $this->suspensionSuzugGroup->save($data);
    }
    
    public function saveReason(Container $data) : int{
        return $this->suspensionReason->save($data);
    }
    
    public function changeSuzugTypeStatus(int $id) : void {
        $item = $this->suspensionSuzugType->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionSuzugType->disable($id);
        }
        else{
            $this->suspensionSuzugType->enable($id);
        }
    }
    
    public function changeSuzugGroupStatus(int $id) : void {
        $item = $this->suspensionSuzugGroup->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionSuzugGroup->disable($id);
        }
        else{
            $this->suspensionSuzugGroup->enable($id);
        }
    }
    
    public function changeReasonStatus(int $id) : void {
        $item = $this->suspensionReason->getById($id);
        $active = $item->get('active');
        if($active){
            $this->suspensionReason->disable($id);
        }
        else{
            $this->suspensionReason->enable($id);
        }
    }
    
}

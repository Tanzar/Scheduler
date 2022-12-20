<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\QualificationService as QualificationService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of AdminPanelQualification
 *
 * @author Tanzar
 */
class AdminPanelQualification extends Controller{
    private QualificationService $qualification;
    
    public function __construct() {
        $this->qualification = new QualificationService();
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function getPersons() : void {
        $data = $this->getRequestData();
        $name = $data->get('name');
        $surname = $data->get('surname');
        $response = $this->qualification->getAllPersonsByNameAndSurname($name, $surname);
        $this->setResponse($response);
    }
    
    public function getPersonCourses() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getAllPersonCourses($id);
        $this->setResponse($response);
    }
    
    public function getPersonEducations() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getAllPersonEducations($id);
        $this->setResponse($response);
    }
    
    public function getPersonQualifications() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getAllPersonQualifications($id);
        $this->setResponse($response);
    }
    
    public function getQualificationOptions() : void {
        $response = $this->qualification->getQualificationOptions();
        $this->setResponse($response);
    }
    
    public function getSupervisionLevels() : void {
        $response = $this->qualification->getAllSupervisionLevels();
        $this->setResponse($response);
    }
    
    public function getOugOffices() : void {
        $response = $this->qualification->getAllOugOffices();
        $this->setResponse($response);
    }
    
    public function getFacilityTypes() : void {
        $response = $this->qualification->getAllFacilityTypes();
        $this->setResponse($response);
    }
    
    public function getEducationLevels() : void {
        $response = $this->qualification->getEducationLevels();
        $this->setResponse($response);
    }
    
    public function savePerson() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->savePerson($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveCourse() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveCourse($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveEducation() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveEducation($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveQualification() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveQualification($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveSupervisionLevel() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveSupervisionLevel($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveOugOffice() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveOugOffice($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function saveFacilityType() : void {
        $data = $this->getRequestData();
        $id = $this->qualification->saveFacilityType($data);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($id, 'id');
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changePersonStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changePersonStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeCourseStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeCourseStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeEducationStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeEducationStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeQualificationStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeQualificationStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeSupervisionLevelStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeSupervisionLevelStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeOugOfficeStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeOugOfficeStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function changeFacilityTypeStatus() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->changeFacilityTypeStatus($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
}

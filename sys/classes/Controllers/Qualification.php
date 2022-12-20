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
 * Description of Qualification
 *
 * @author Tanzar
 */
class Qualification extends Controller{
    private QualificationService $qualification;
    
    public function __construct() {
        $this->qualification = new QualificationService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('qualification_user');
        parent::__construct($privilages);
    }
    
    public function getPersons() : void {
        $data = $this->getRequestData();
        $name = $data->get('name');
        $surname = $data->get('surname');
        $response = $this->qualification->getActivePersonsByNameAndSurname($name, $surname);
        $this->setResponse($response);
    }
    
    public function getPersonCourses() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getActivePersonCourses($id);
        $this->setResponse($response);
    }
    
    public function getPersonEducations() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getActivePersonEducations($id);
        $this->setResponse($response);
    }
    
    public function getPersonQualifications() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id_person');
        $response = $this->qualification->getActivePersonQualifications($id);
        $this->setResponse($response);
    }
    
    public function getQualificationOptions() : void {
        $response = $this->qualification->getQualificationOptions();
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
    
    public function removePerson() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->disablePerson($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeCourse() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->disableCourse($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeEducation() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->disableEducation($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeQualification() {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->qualification->disableQualification($id);
        $languages = Languages::getInstance();
        $response = new Container();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

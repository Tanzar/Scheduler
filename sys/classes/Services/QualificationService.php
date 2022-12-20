<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\CoursesDAO as CoursesDAO;
use Data\Access\Tables\EducationDAO as EducationDAO;
use Data\Access\Tables\FacilityTypeDAO as FacilityTypeDAO;
use Data\Access\Tables\OugOfficesDAO as OugOfficesDAO;
use Data\Access\Tables\PersonDAO as PersonDAO;
use Data\Access\Tables\QualificationDAO as QualificationDAO;
use Data\Access\Tables\SupervisionLevelDAO as SupervisionLevelDAO;
use Data\Access\Views\QualificationsDetailsView as QualificationsDetailsView;
use Custom\Parsers\Database\Qualification as Qualification;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of QualificationService
 *
 * @author Tanzar
 */
class QualificationService {
    private CoursesDAO $courses;
    private EducationDAO $education;
    private FacilityTypeDAO $facilityType;
    private OugOfficesDAO $ougOffices;
    private PersonDAO $person;
    private QualificationDAO $qualification;
    private SupervisionLevelDAO $supervisionLevel;
    private QualificationsDetailsView $qualificationsDetails;


    public function __construct() {
        $this->courses = new CoursesDAO();
        $this->education = new EducationDAO();
        $this->facilityType = new FacilityTypeDAO();
        $this->ougOffices = new OugOfficesDAO();
        $this->person = new PersonDAO();
        $this->qualification = new QualificationDAO();
        $this->supervisionLevel = new SupervisionLevelDAO();
        $this->qualificationsDetails = new QualificationsDetailsView();
    }
    
    public function getAllPersons() : Container {
        return $this->person->getAll();
    }
    
    public function getActivePersons() : Container {
        return $this->person->getActive();
    }
    
    public function getAllPersonsByNameAndSurname(string $name, string $surname) : Container {
        return $this->person->getAllByNameAndSurname($name, $surname);
    }
    
    public function getActivePersonsByNameAndSurname(string $name, string $surname) : Container {
        return $this->person->getActiveByNameAndSurname($name, $surname);
    }
    
    public function getQualificationOptions() : Container {
        $result = new Container();
        $ougOffices = $this->ougOffices->getActive();
        $result->add($ougOffices->toArray(), 'oug');
        $facilityTypes = $this->facilityType->getActive();
        $result->add($facilityTypes->toArray(), 'facilityTypes');
        $supervisionLevels = $this->supervisionLevel->getActive();
        $result->add($supervisionLevels->toArray(), 'supervisionLevels');
        return $result;
    }
    
    public function getAllOugOffices() : Container {
        return $this->ougOffices->getAll();
    }
    
    public function getActiveOugOffices() : Container {
        return $this->ougOffices->getActive();
    }
    
    public function getAllFacilityTypes() : Container {
        return $this->facilityType->getAll();
    }
    
    public function getActivefacilityTypes() : Container {
        return $this->facilityType->getActive();
    }
    
    public function getAllSupervisionLevels() : Container {
        return $this->supervisionLevel->getAll();
    }
    
    public function getActiveSupervisionLevels() : Container {
        return $this->supervisionLevel->getActive();
    }
    
    public function getAllPersonCourses(int $idPerson) : Container {
        return $this->courses->getAllByIdPerson($idPerson);
    }
    
    public function getActivePersonCourses(int $idPerson) : Container {
        return $this->courses->getActiveByIdPerson($idPerson);
    }
    
    public function getAllPersonEducations(int $idPerson) : Container {
        return $this->education->getAllByIdPerson($idPerson);
    }
    
    public function getActivePersonEducations(int $idPerson) : Container {
        return $this->education->getActiveByIdPerson($idPerson);
    }
    
    public function getAllPersonQualifications(int $idPerson) : Container {
        return $this->qualificationsDetails->getAllByIdPerson($idPerson);
    }
    
    public function getActivePersonQualifications(int $idPerson) : Container {
        return $this->qualificationsDetails->getActiveByIdPerson($idPerson);
    }
    
    public function getEducationLevels() : Container {
        $languages = Languages::getInstance();
        $educationLevels = $languages->get('education_level');
        $result = new Container();
        foreach ($educationLevels as $value => $text) {
            $result->add(array(
                'title' => $text,
                'value' => $value
            ));
        }
        return $result;
    }
    
    public function savePerson(Container $data) : int {
        return $this->person->save($data);
    }
    
    public function saveCourse(Container $data) : int {
        return $this->courses->save($data);
    }
    
    public function saveEducation(Container $data) : int {
        return $this->education->save($data);
    }
    
    public function saveFacilityType(Container $data) : int {
        return $this->facilityType->save($data);
    }
    
    public function saveOugOffice(Container $data) : int {
        return $this->ougOffices->save($data);
    }
    
    public function saveQualification(Container $data) : int {
        $parser = new Qualification();
        $parsed = $parser->parse($data);
        return $this->qualification->save($parsed);
    }
    
    public function saveSupervisionLevel(Container $data) : int {
        return $this->supervisionLevel->save($data);
    }
    
    public function changeCourseStatus(int $id) : void {
        $item = $this->courses->getById($id);
        $active = $item->get('active');
        if($active){
            $this->courses->disable($id);
        }
        else{
            $this->courses->enable($id);
        }
    }
    
    public function changeEducationStatus(int $id) : void {
        $item = $this->education->getById($id);
        $active = $item->get('active');
        if($active){
            $this->education->disable($id);
        }
        else{
            $this->education->enable($id);
        }
    }
    
    public function changeFacilityTypeStatus(int $id) : void {
        $item = $this->facilityType->getById($id);
        $active = $item->get('active');
        if($active){
            $this->facilityType->disable($id);
        }
        else{
            $this->facilityType->enable($id);
        }
    }
    
    public function changeOugOfficeStatus(int $id) : void {
        $item = $this->ougOffices->getById($id);
        $active = $item->get('active');
        if($active){
            $this->ougOffices->disable($id);
        }
        else{
            $this->ougOffices->enable($id);
        }
    }
    
    public function changePersonStatus(int $id) : void {
        $item = $this->person->getById($id);
        $active = $item->get('active');
        if($active){
            $this->person->disable($id);
        }
        else{
            $this->person->enable($id);
        }
    }
    
    public function changeQualificationStatus(int $id) : void {
        $item = $this->qualification->getById($id);
        $active = $item->get('active');
        if($active){
            $this->qualification->disable($id);
        }
        else{
            $this->qualification->enable($id);
        }
    }
    
    public function changeSupervisionLevelStatus(int $id) : void {
        $item = $this->supervisionLevel->getById($id);
        $active = $item->get('active');
        if($active){
            $this->supervisionLevel->disable($id);
        }
        else{
            $this->supervisionLevel->enable($id);
        }
    }
    
    public function disableCourse(int $id) : void {
        $this->courses->disable($id);
    }
    
    public function disableEducation(int $id) : void {
        $this->education->disable($id);
    }
    
    public function disableFacilityType(int $id) : void {
        $this->facilityType->disable($id);
    }
    
    public function disableOugOffice(int $id) : void {
        $this->ougOffices->disable($id);
    }
    
    public function disablePerson(int $id) : void {
        $this->person->disable($id);
    }
    
    public function disableQualification(int $id) : void {
        $this->qualification->disable($id);
    }
    
    public function disableSupervisionLevel(int $id) : void {
        $this->supervisionLevel->disable($id);
    }
}

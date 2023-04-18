<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\PositionGroupsDAO as PositionGroupsDAO;
use Data\Access\Tables\CourtApplicationDAO as CourtApplicationDAO;
use Data\Access\Views\CourtApplicationDetailsView as CourtApplicationDetailsView;
use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Custom\Parsers\Database\CourtApplication as CourtApplication;
use Custom\Blockers\InspectorDateBlocker as InspectorDateBlocker;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Services\Exceptions\SystemBlockedException as SystemBlockedException;


/**
 * Description of CourtApplicationService
 *
 * @author Tanzar
 */
class CourtApplicationService {
    private PositionGroupsDAO $positionGroups;
    private CourtApplicationDAO $courtApplication;
    private CourtApplicationDetailsView $courtApplicationDetails;
    private DocumentDetailsView $documentDetails;
    private DocumentUserDetailsView $documentUserDetails;
    
    public function __construct() {
        $this->positionGroups = new PositionGroupsDAO();
        $this->courtApplication = new CourtApplicationDAO();
        $this->courtApplicationDetails = new CourtApplicationDetailsView();
        $this->documentDetails = new DocumentDetailsView();
        $this->documentUserDetails = new DocumentUserDetailsView();
    }
    
    public function getNewApplicationDetails(int $documentId) : Container {
        $details = new Container();
        $document = $this->documentDetails->getById($documentId);
        $details->add($document->get('start'), 'start');
        $details->add($document->get('end'), 'end');
        $groups = $this->positionGroups->getActive();
        $details->add($groups->toArray(), 'groups');
        return $details;
    }
    
    public function getAllUserApplictionsForYear(string $username, int $year) : Container {
        return $this->courtApplicationDetails->getAllByUsernameAndYear($username, $year);
    }
    
    public function getApplicationsForCurrentUser(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->courtApplicationDetails->getActiveByUsernameAndDocument($username, $documentId);
    }
    
    public function getCurrentUserActiveApplicationsByYear(int $year) : Container {
        $username = Session::getUsername();
        return $this->courtApplicationDetails->getUserActiveByYear($username, $year);
    }
    
    public function getActiveApplicationsByMonthAndYear(int $month, int $year) : Container {
        return $this->courtApplicationDetails->getActiveByMonthAndYear($month, $year);
    }
    
    public function saveNewApplication(Container $data) : int {
        $this->checkBlocker($data);
        $username = Session::getUsername();
        $documentId = (int) $data->get('id_document');
        $details = $this->documentUserDetails->getByUsernameAndDocumentId($username, $documentId);
        $documentUserId = $details->get('id_document_user');
        $data->add($documentUserId, 'id_document_user', true);
        $parser = new CourtApplication();
        $application = $parser->parse($data);
        return $this->courtApplication->save($application);
    }
    
    public function saveApplication(Container $data) : int {
        $this->checkBlocker($data);
        $parser = new CourtApplication();
        $application = $parser->parse($data);
        return $this->courtApplication->save($application);
    }
    
    public function disable(int $id) : void {
        $application = $this->courtApplication->getById($id);
        $this->checkBlocker($application);
        $this->courtApplication->disable($id);
    }
    
    public function changeApplicationStatus(int $id) : void {
        $application = $this->courtApplication->getById($id);
        $this->checkBlocker($application);
        $active = $application->get('active');
        if($active){
            $this->courtApplication->disable($id);
        }
        else{
            $this->courtApplication->enable($id);
        }
    }
    
    private function checkBlocker(Container $data) {
        $blocker = new InspectorDateBlocker();
        if($blocker->isBLocked($data)){
            throw new SystemBlockedException();
        }
    }
}

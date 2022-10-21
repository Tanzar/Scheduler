<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Services;

use Data\Access\Tables\InstrumentUsageDAO as InstrumentUsageDAO;
use Data\Access\Views\InstrumentUsageDetailsView as InstrumentUsageDetailsView;
use Data\Access\Views\EquipmentDetailsView as EquipmentDetailsView;
use Data\Access\Views\DocumentDetailsView as DocumentDetailsView;
use Data\Access\Views\DocumentUserDetailsView as DocumentUserDetailsView;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Custom\Parsers\Database\InstrumentUsage as InstrumentUsage;

/**
 * Description of InstrumentUsageService
 *
 * @author Tanzar
 */
class InstrumentUsageService {
    private EquipmentDetailsView $equipmentDetails;
    private InstrumentUsageDAO $instrumentUsage;
    private InstrumentUsageDetailsView $instrumentUsageDetails;
    private DocumentDetailsView $documentDetails;
    private DocumentUserDetailsView $documentUserDetails;
    
    public function __construct() {
        $this->equipmentDetails = new EquipmentDetailsView();
        $this->instrumentUsage = new InstrumentUsageDAO();
        $this->instrumentUsageDetails = new InstrumentUsageDetailsView();
        $this->documentDetails = new DocumentDetailsView();
        $this->documentUserDetails = new DocumentUserDetailsView();
    }
    
    public function getNewUsageDetails(int $documentId) : Container {
        $details = new Container();
        $document = $this->documentDetails->getById($documentId);
        $details->add($document->get('start'), 'start');
        $details->add($document->get('end'), 'end');
        $instruments = $this->equipmentDetails->getActiveMeasurementInstruments();
        $details->add($instruments->toArray(), 'instruments');
        return $details;
    }
    
    public function getUsagedForCurrentUser(int $documentId) : Container {
        $username = Session::getUsername();
        return $this->instrumentUsageDetails->getActiveByDocumentAndUsername($documentId, $username);
    }
    
    public function saveUsageForCurrentUser(Container $data) : int {
        $username = Session::getUsername();
        $documentId = (int) $data->get('id_document');
        $details = $this->documentUserDetails->getByUsernameAndDocumentId($username, $documentId);
        $documentUserId = $details->get('id_document_user');
        $data->add($documentUserId, 'id_document_user', true);
        $parser = new InstrumentUsage();
        $usage = $parser->parse($data);
        return $this->instrumentUsage->save($usage);
    }
    
    public function updateUsage(Container $data) : void {
        $parser = new InstrumentUsage();
        $usage = $parser->parse($data);
        $this->instrumentUsage->save($usage);
    }
    
    public function disableUsage(int $id) : void {
        $this->instrumentUsage->disable($id);
    }
    
    public function changeUsageStatus(int $id) : void {
        $usage = $this->instrumentUsage->getById($id);
        $active = $usage->get('active');
        if($active){
            $this->instrumentUsage->disable($id);
        }
        else{
            $this->instrumentUsage->enable($id);
        }
    }
}
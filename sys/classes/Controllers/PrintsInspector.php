<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\PrintsService as PrintsService;
use Services\DocumentService as DocumentService;
use Tanweb\Container as Container;
use Tanweb\Session as Session;

/**
 * Description of PrintsInspector
 *
 * @author Tanzar
 */
class PrintsInspector extends Controller{
    private PrintsService $prints;
    private DocumentService $document;

    public function __construct() {
        $this->prints = new PrintsService();
        $this->document = new DocumentService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_inspector');
        $privilages->add('prints_inspector_all_documents');
        parent::__construct($privilages);
    }
    
    public function getDocuments() : void {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        if($this->currentUserHavePrivilage('admin') || 
                $this->currentUserHavePrivilage('prints_inspector_all_documents')){
            $response = $this->document->getDocumentsByYear($year);
        }
        else{
            $response = $this->document->getCurrentUserDocumentsByYear($year);
        }
        $this->setResponse($response);
    }
    
    public function generateInstrumentUsageCard() : void {
        $data = $this->getRequestData();
        $instrumentId = (int) $data->get('id');
        $year = (int) $data->get('year');
        $this->prints->generateInstrumentUsageCard($instrumentId, $year);
    }
    
    public function generateDocumentRaport() : void {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id');
        $this->prints->generateDocumentRaport($documentId);
    }
}

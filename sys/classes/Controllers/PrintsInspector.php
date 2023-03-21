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
use Tanweb\Logger\Logger as Logger;
use Custom\Logs\PrintsLog as PrintsLog;

/**
 * Description of PrintsInspector
 *
 * @author Tanzar
 */
class PrintsInspector extends Controller{
    private PrintsService $prints;
    private DocumentService $document;
    private Logger $logger;

    public function __construct() {
        $this->prints = new PrintsService();
        $this->document = new DocumentService();
        $this->logger = Logger::getInstance();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_inspector');
        $privilages->add('prints_inspector_all_documents');
        parent::__construct($privilages);
    }
    
    public function getDocuments() : void {
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $month = (int) $data->get('month');
        if($this->currentUserHavePrivilage('admin') || 
                $this->currentUserHavePrivilage('prints_inspector_all_documents')){
            $response = $this->document->getDocumentsByMonthYear($month, $year);
        }
        else{
            $response = $this->document->getCurrentUserDocumentsByMonthYear($year, $year);
        }
        $this->setResponse($response);
    }
    
    public function generateInstrumentUsageCard() : void {
        $data = $this->getRequestData();
        $instrumentId = (int) $data->get('id');
        $year = (int) $data->get('year');
        $this->prints->generateInstrumentUsageCard($instrumentId, $year);
        $username = Session::getUsername();
        $entry = new PrintsLog('user: ' . $username . ' generated instrument usage card'
                . ' for id_instrument =' . $instrumentId . ' and year = ' . $year);
        $this->logger->log($entry);
    }
    
    public function generateDocumentRaport() : void {
        $data = $this->getRequestData();
        $documentId = (int) $data->get('id');
        $this->prints->generateDocumentRaport($documentId);
        $username = Session::getUsername();
        $entry = new PrintsLog('user: ' . $username . ' generated document raport'
                . ' for id_document =' . $documentId);
        $this->logger->log($entry);
    }
}

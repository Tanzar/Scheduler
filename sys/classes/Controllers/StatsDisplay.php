<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\StatsService as StatsService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of StatsDisplay
 *
 * @author Tanzar
 */
class StatsDisplay extends Controller{
    private StatsService $stats;

    public function __construct() {
        $this->stats = new StatsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('stats_admin');
        $privilages->add('stats_user');
        parent::__construct($privilages);
    }
    
    public function getStats() : void {
        $response = $this->stats->getActive();
        $this->setResponse($response);
    }
    
    public function getInputsSettings() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $response = $this->stats->getInputSettings($id);
        $this->setResponse($response);
    }
    
    public function generateStats() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        if(is_string($data->get('inputs'))){
            $json = json_decode($data->get('inputs'), true, 512, JSON_UNESCAPED_UNICODE);
            $inputs = new Container($json);
        }
        else{
            $inputs = new Container($data->get('inputs'));
        }
        $response = $this->stats->generate($id, $inputs);
        $this->setResponse($response);
    }
    
    public function generatePDF() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        if(is_string($data->get('inputs'))){
            $json = json_decode($data->get('inputs'), true, 512, JSON_UNESCAPED_UNICODE);
            $inputs = new Container($json);
        }
        else{
            $inputs = new Container($data->get('inputs'));
        }
        $this->stats->generatePDF($id, $inputs);
    }
    
    public function generateXlsx() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        if(is_string($data->get('inputs'))){
            $json = json_decode($data->get('inputs'), true, 512, JSON_UNESCAPED_UNICODE);
            $inputs = new Container($json);
        }
        else{
            $inputs = new Container($data->get('inputs'));
        }
        $this->stats->generateXLSX($id, $inputs);
    }
}

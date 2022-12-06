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
        $response = $this->stats->getActiveStats();
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
        $response = $this->stats->generateStats($data);
        $this->setResponse($response);
    }
}

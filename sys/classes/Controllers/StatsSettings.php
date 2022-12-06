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
 * Description of StatsSettings
 *
 * @author Tanzar
 */
class StatsSettings extends Controller{
    private StatsService $stats;

    public function __construct() {
        $this->stats = new StatsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('stats_admin');
        parent::__construct($privilages);
    }
    
    public function getStatsWithoutForm() : void {
        $response = $this->stats->getAllStatsWithoutForm();
        $this->setResponse($response);
    }
    
    public function getStatsStageOne() : void {
        $response = $this->stats->getStatsSettingsStageOne();
        $this->setResponse($response);
    }
    
    public function getStatsStageTwo() : void {
        $data = $this->getRequestData();
        $response = $this->stats->getStatsSettingsStageTwo($data);
        $this->setResponse($response);
    }
    
    public function saveStats() : void {
        $data = $this->getRequestData();
        $id = $this->stats->saveStats($data);
        $response = new Container();
        $response->add($id, 'id');
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
    
    public function removeStats() : void {
        $data = $this->getRequestData();
        $id = (int) $data->get('id');
        $this->stats->removeStats($id);
        $response = new Container();
        $languages = Languages::getInstance();
        $response->add($languages->get('changes_saved'), 'message');
        $this->setResponse($response);
    }
}

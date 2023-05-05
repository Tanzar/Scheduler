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
 * Description of StatsOverlord
 *
 * @author Tanzar
 */
class StatsOverlord extends Controller{
    private StatsService $stats;

    public function __construct() {
        $this->stats = new StatsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('stats_admin');
        parent::__construct($privilages);
    }
    
    public function getInspections() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $response = $this->stats->formInspectionStats($month, $year);
        $this->setResponse($response);
    }
}

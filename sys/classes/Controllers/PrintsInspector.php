<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\PrintsService as PrintsService;
use Tanweb\Container as Container;

/**
 * Description of PrintsInspector
 *
 * @author Tanzar
 */
class PrintsInspector extends Controller{
    private PrintsService $prints;

    public function __construct() {
        $this->prints = new PrintsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_inspector');
        parent::__construct($privilages);
    }
    
    public function generateInstrumentUsageCard() : void {
        $data = $this->getRequestData();
        $instrumentId = (int) $data->get('id');
        $year = (int) $data->get('year');
        $this->prints->generateInstrumentUsageCard($instrumentId, $year);
    }
}

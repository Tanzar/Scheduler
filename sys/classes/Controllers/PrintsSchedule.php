<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Services\PrintsService as PrintsService;
use Tanweb\Container as Container;
use Tanweb\Config\INI\Languages as Languages;

/**
 * Description of PrintsSchedule
 *
 * @author Tanzar
 */
class PrintsSchedule extends Controller{
    private PrintsService $prints;

    public function __construct() {
        $this->prints = new PrintsService();
        $privilages = new Container();
        $privilages->add('admin');
        $privilages->add('prints_schedule');
        parent::__construct($privilages);
    }
    
    public function generateAttendanceList() {
        $data = $this->getRequestData();
        $month = (int) $data->get('month');
        $year = (int) $data->get('year');
        $this->prints->generateAttendanceList($month, $year);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Session as Session;
use Services\IndexService as IndexService;

/**
 * Description of Index
 *
 * @author Tanzar
 */
class Index extends Controller{
    
    public function __construct() {
        $privilages = new Container();
        parent::__construct($privilages);
    }
    
    public function getReport(){
        $data = $this->getRequestData();
        $year = (int) $data->get('year');
        $username = Session::getUsername();
        $result = IndexService::getUserData($year, $username);
        $this->setResponse($result);
    }
}

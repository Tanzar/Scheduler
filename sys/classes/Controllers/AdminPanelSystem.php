<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Custom\DBFixer\DBFixer as DBFixer;

/**
 * Description of AdminPanelSystem
 *
 * @author Tanzar
 */
class AdminPanelSystem extends Controller{
    
    public function __construct() {
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function runFix(){
        $text = DBFixer::run();
        $response = new Container();
        $response->add($text, 'message');
        $this->setResponse($response);
    }
}

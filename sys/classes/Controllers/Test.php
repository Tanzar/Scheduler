<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;


/**
 * Description of Test
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Test extends Controller{
    
    public function __construct() {
        $privilages = new Container();
        $privilages->add('user');
        parent::__construct($privilages);
    }
    
    public function test(){
    }
    
}

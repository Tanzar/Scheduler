<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Mailing\Postman as Postman;
use Tanweb\Mailing\Email as Email;

/**
 * Description of Test
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class Test extends Controller{
    
    public function __construct() {
        $privilages = new Container();
        $privilages->add('admin');
        parent::__construct($privilages);
    }
    
    public function test(){
        
    }
    
}

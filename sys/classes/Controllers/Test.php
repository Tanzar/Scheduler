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
        $indexes = new Container();
        $indexes->add('tanweb');
        $privilages = new Container();
        $privilages->add('user');
        parent::__construct($indexes, $privilages);
    }
    
    public function test(){
        $sql = new \Tanweb\Database\SQL\MysqlBuilder();
        $sql->select('user');
        $data = $this->select('tanweb', $sql);
        $this->setResponse($data);
        $app = $this->getConfigValue('name');
        $container = new Container();
        $container->add($app);
        $this->setResponse($container);
    }
    
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of AdminController
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class AdminPanel extends Controller{
    
    public function __construct() {
        $databases = new Container(['scheduler']);
        $privilages = new Container(['admin']);
        parent::__construct($databases, $privilages);
    }
    
    public function getAllUsers() {
        $sql = new MysqlBuilder();
        $sql->select('user');
        $result = $this->select('scheduler', $sql);
        $this->setResponse($result);
    }
    
    
}

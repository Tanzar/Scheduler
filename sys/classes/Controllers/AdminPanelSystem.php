<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Custom\DBFixer\DBFixer as DBFixer;
use Custom\Backup\Backup as Backup;
use Custom\Cleaner\Cleaner as Cleaner;
use Tanweb\Config\INI\Languages as Laguages;

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
    
    public function runBackup() : void {
        Backup::run();
        $languages = Laguages::getInstance();
        $response = new Container();
        $response->add($languages->get('backup_complete'), 'message');
        $this->setResponse($response);
    }
    
    public function runCleaner() : void {
        $count = Cleaner::run();
        $languages = Laguages::getInstance();
        $response = new Container();
        $response->add($languages->get('removed') . ': ' . $count, 'message');
        $this->setResponse($response);
    }
}

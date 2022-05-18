<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Container as Container;
use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Config\Template as Template;
use Exception;

/**
 * Description of TestFile
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class TestFile extends Controller{
    
    public function __construct() {
        $indexes = new Container();
        $indexes->add('tanweb');
        parent::__construct($indexes);
    }
    
    public function test(){
        $path = Template::getLocalPath('example.xlsx');
        $xlsx = new ExcelEditor();
        $xlsx->newFile('test', 'pjerwszy');
        $xlsx->writeToCell('pjerwszy', 'A1', 'Hello World');
        $xlsx->sendToBrowser('testowy');
    }
    
}

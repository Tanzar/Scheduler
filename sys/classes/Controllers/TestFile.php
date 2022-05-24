<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\File\ExcelEditor as ExcelEditor;

/**
 * Description of TestFile
 *
 * @author Grzegorz Spakowski, Tanzar
 */
class TestFile extends Controller{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function test(){
        $xlsx = new ExcelEditor();
        $xlsx->newFile('test', 'pjerwszy');
        $xlsx->writeToCell('pjerwszy', 'A1', 'Hello World');
        $xlsx->sendToBrowser('testowy');
    }
    
}

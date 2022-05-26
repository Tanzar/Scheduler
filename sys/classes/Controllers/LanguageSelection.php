<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Controllers;

use Controllers\Base\Controller as Controller;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Container as Container;

/**
 * Description of LanguageSelection
 *
 * @author Tanzar
 */
class LanguageSelection extends Controller{
    
    public function getPackage(){
        $language = Languages::getInstance();
        $package = $language->getPackage();
        $this->setResponse($package);
    }
    
    public function getInterfaceNames() {
        $language = Languages::getInstance();
        $package = $language->get('interface');
        $data = new Container($package);
        $this->setResponse($data);
    }
}

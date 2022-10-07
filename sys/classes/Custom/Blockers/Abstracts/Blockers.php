<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Blockers\Abstracts;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use JsonSerializable;

/**
 * Description of Blockers
 *
 * @author Tanzar
 */
class Blockers implements JsonSerializable{
    
    protected string $index;
    protected function __construct(string $index) {
        $this->index = $index;
    }
    
    public static function INSPECTOR() : self {
        return new self('INSPECTOR');
    }
    
    public static function SCHEDULE() : self {
        return new self('SCHEDULE');
    }
    
    public function getConfigValue() : int {
        $appConfig = AppConfig::getInstance();
        $cfg = $appConfig->getAppConfig();
        switch($this->index){
            case 'SCHEDULE':
                return $this->getLockSchedule($cfg);
            case 'INSPECTOR':
                return (int) $cfg->get('lockInspector');
            default:
                return 0;
        }
    }
    
    private function getLockSchedule(Container $cfg) : int {
        $day = (int) $cfg->get('lockSchedule');
        if($day < 1) {
            $day = 1;
        }
        if($day > 20) {
            $day = 20;
        }
        return $day;
    }
    
    public function jsonSerialize() : array {
        return get_object_vars($this);
    }
}

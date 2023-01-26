<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\DBFixer;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use Custom\DBFixer\FixerEntry as FixerEntry;
use Custom\DBFixer\FixerReport as FixerReport;
use Custom\DBFixer\Fixers\InventoryFixer as InventoryFixer;
use Custom\DBFixer\Fixers\SystemFixer as SystemFixer;
use Tanweb\Logger\Logger as Logger;

/**
 * Description of DBFixer
 *
 * @author Tanzar
 */
class DBFixer {
    
    public static function run() : string {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $tasks = new Container($cfg->get('tasks'));
        $isFixSet = $tasks->get('fix');
        if($isFixSet){
            self::logStart();
            $report = self::runFixers();
            self::logEnd($report);
            return $report->toString();
        }
        return '';
    }
    
    private static function logStart() : void {
        $logger = Logger::getInstance();
        $entry = new FixerEntry('Fixer task started');
        $logger->log($entry);
    }
    
    private static function runFixers() : FixerReport {
        $report = new FixerReport();
        SystemFixer::run($report);
        InventoryFixer::run($report);
        return $report;
    }
    
    private static function logEnd(FixerReport $report) : void {
        $logger = Logger::getInstance();
        $entry = new FixerEntry('Fixer task finished: ' . $report->toString());
        $logger->log($entry);
    }
    
}

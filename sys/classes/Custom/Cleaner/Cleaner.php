<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Cleaner;

use Tanweb\Container as Container;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Cleaner\CleanerEntry as CleanerEntry;
use Tanweb\Logger\Logger as Logger;
use Tanweb\Database\Database as Database;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;

/**
 * Description of Cleaner
 *
 * @author Tanzar
 */
class Cleaner {
    private static $tables = ['art_41', 'courses', 'court_application', 'days_off',
        'decision', 'document', 'education', 'employment', 'instrument_usage', 
        'inventory_log', 'overtime_reduction', 'person', 'privilages', 'qualifications',
        'schedule', 'statistics', 'suspension', 'suzug_user', 'ticket'];
    
    
    public static function run() : int {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $tasks = new Container($cfg->get('tasks'));
        $isCleanerSet = $tasks->get('clean');
        if($isCleanerSet){
            $logger = Logger::getInstance();
            $logger->log(new CleanerEntry('Clean started'));
            $count = self::clean();
            $logger->log(new CleanerEntry('Clean finished, removed: ' . $count));
            return $count;
        }
        return 0;
    }
    
    private static function clean() : int {
        $count = 0;
        $database = Database::getInstance('scheduler');
        foreach (self::$tables as $table) {
            $sql = new MysqlBuilder();
            $sql->select($table)->where('active', 0);
            $data = $database->select($sql);
            foreach ($data->toArray() as $item) {
                $id = (int) $item['id'];
                $count++;
                $deleteSQL = new MysqlBuilder();
                $deleteSQL->delete($table, 'id', $id);
                $database->delete($deleteSQL);
            }
        }
        return $count;
    }
}

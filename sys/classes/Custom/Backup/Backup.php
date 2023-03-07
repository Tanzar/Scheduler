<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Backup;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Database\Database as Database;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Container as Container;
use Tanweb\Logger\Logger as Logger;
use Custom\Backup\BackupEntry as BackupEntry;
use mysqli;
use DateTime;

/**
 * Description of Backup
 *
 * @author Tanzar
 */
class Backup {
    
    public static function run() : void {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $tasks = new Container($cfg->get('tasks'));
        $isBackupSet = $tasks->get('backup');
        if($isBackupSet){
            $logger = Logger::getInstance();
            $logger->log(new BackupEntry('Backup started'));
            self::makeBackup($appconfig);
            $logger->log(new BackupEntry('Backup finished'));
        }
    }
    
    private static function makeBackup(AppConfig $appconfig) : void {
        $cfg = $appconfig->getAppConfig();
        $path = $cfg->get('backups_path');
        $limit = (int) $cfg->get('backups_count');
        self::manageFiles($path, $limit);
        self::makeFile($appconfig, $path);
    }
    
    private static function manageFiles(string $path, int $limit) : void {
        $files = glob($path . '/*.sql');
        usort($files, function($a, $b) {
            return filemtime($a) - filemtime($b);
        });
        $filesCount = count($files);
        $toDelete = ($filesCount - $limit + 1 < 0) ? 0 : $filesCount - $limit + 1;
        for($i = 0; $i < $toDelete; $i++){
            unlink($files[$i]);
        }
    }
    
    private static function makeFile(AppConfig $appconfig, string $path) : void {
        $tables = self::getTables($appconfig);
        $sqlScript = "";
        foreach ($tables as $table) {
            $sqlScript .= "\n\n";
            $sqlScript .= self::formTable($table);
        }
        self::saveFiles($sqlScript, $path);
    }
    
    private static function getTables(AppConfig $appconfig) : array {
        $cfg = $appconfig->getDatabase('scheduler');
        $user = $cfg->get('user');
        $pass = $cfg->get('pass');
        $host = $cfg->get('host');
        $charset = $cfg->get('charset');
        $conn = new mysqli($host, $user, $pass, 'scheduler');
        $conn->set_charset($charset);
        $result = mysqli_query($conn, "SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        $tables = array();

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        return $tables;
    }
    
    private static function formTable(string $table) : string {
        $db = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select($table);
        $sqlScript = "INSERT INTO `$table` ";
        $result = $db->select($sql);
        $count = $result->length();
        if($count > 0){
            $cols = self::formColumns($result->get(0));
            $sqlScript .= $cols . " VALUES \n";
            foreach ($result->toArray() as $index => $row) {
                $values = self::formVals($table, $row);
                $sqlScript .= $values;
                if((int) $index < ($count - 1)){
                    $sqlScript .= ",\n";
                }
                else{
                    $sqlScript .= ";\n";
                }
            }
            $sqlScript .= "\n"; 
            return $sqlScript;
        }
        else{
            return "\n";
        }
    }
    
    private static function formColumns(array $row) : string {
        $cols = "(";
        $columnCount = count($row);
        $j = 0;
        foreach ($row as $key => $value) {
            $cols .= '`' . $key . '`';
            if ($j < ($columnCount - 1)) {
                $cols .= ',';
            }
            $j++;
        }
        return $cols . ")";
    }
    
    private static function formVals(string $table, array $row) : string {
        $vals = '';
        $columnCount = count($row);
        $j = 0;
        foreach ($row as $key => $value) {
            $text = str_replace("\0", "", $value);
            $vals .= (isset($value)) ? "'" . $text . "'" : "''";
            if ($j < ($columnCount - 1)) {
                $vals .= ',';
            }
            $j++;
        }
        return "(" . $vals  . ")";
    }
    
    private static function saveFiles(string $sqlScript, string $path) : void {
        $timestamp = new DateTime();
        $name = $timestamp->format('Y-m-d_[H_i_s]');
        $backup = $path . '/' . $name . '.sql';
        $mysql_file = fopen($backup, 'w+');
        fwrite($mysql_file ,$sqlScript);
        fclose($mysql_file);
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Backup;

use Tanweb\Config\INI\AppConfig as AppConfig;
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
        $conn = self::connect($appconfig);
        $tables = self::getTables($conn);
        $sqlScript = "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;\n" .
            "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n" .
            "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';\n";
        foreach ($tables as $table) {
            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_row($result);

            $sqlScript .= "\n\n";
            $sqlScript .= self::formTable($table, $conn);
        }
        $sqlScript .= "\nSET SQL_MODE=@OLD_SQL_MODE;\n" .
            "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;\n" .
            "SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
            ";
        self::saveFiles($sqlScript, $path);
    }
    
    private static function connect(AppConfig $appconfig) : mysqli {
        $cfg = $appconfig->getDatabase('scheduler');
        $user = $cfg->get('user');
        $pass = $cfg->get('pass');
        $host = $cfg->get('host');
        $charset = $cfg->get('charset');
        $conn = new mysqli($host, $user, $pass, 'scheduler');
        $conn->set_charset($charset);
        return $conn;
    }
    
    private static function getTables(mysqli $conn) : array {
        $result = mysqli_query($conn, "SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        $tables = array();

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
        
        return $tables;
    }
    
    private static function formTable(string $table, mysqli $conn) : string {
        $sqlScript = '';
        $query = "SELECT * FROM $table";
        $result = mysqli_query($conn, $query);
        $columnCount = mysqli_num_fields($result);
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= self::formInsert('scheduler.' . $table, $row, $columnCount) . "\n";
            }
        }
        $sqlScript .= "\n"; 
        return $sqlScript;
    }
    
    private static function formInsert(string $table, array $row, int $columnCount) : string {
        $sqlScript = "INSERT INTO $table VALUES(";
        for ($j = 0; $j < $columnCount; $j ++) {
            $row[$j] = $row[$j];
            $text = str_replace("\0", "", $row[$j]);
            $sqlScript .= (isset($row[$j])) ? "'" . $text . "'" : "''";
            if ($j < ($columnCount - 1)) {
                $sqlScript .= ',';
            }
        }
        $sqlScript .= ");";
        return $sqlScript;
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

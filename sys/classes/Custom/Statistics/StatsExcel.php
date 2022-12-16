<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics;

use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Container as Container;
use Custom\Statistics\Options\Group as Group;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Statistics\Exceptions\UnsupportedStatisticsTypeException as UnsupportedStatisticsTypeException;

/**
 * Description of StatsExcel
 *
 * @author Tanzar
 */
class StatsExcel {
    
    public static function formFile(Container $stats) : void {
        self::typeCheck($stats);
        $xlsx = new ExcelEditor();
        $title = $stats->get('title');
        if($stats->get('type') === 'table'){
            $appconfig = AppConfig::getInstance();
            $cfg = $appconfig->getAppConfig();
            $author = $cfg->get('name') . ' web app';
            $xlsx->newFile($title, $author, 'main');
            self::writeSheet($xlsx, $stats, 'main');
        }
        elseif ($stats->get('type') === 'multiple_tables'){
            self::writeMultipleSheets($xlsx, $stats);
        }
        $xlsx->sendToBrowser($title);
    }
    
    private static function typeCheck(Container $stats) : void {
        if($stats->isValueSet('type')){
            if($stats->get('type') !== 'table' && $stats->get('type') !== 'multiple_tables'){
                throw new UnsupportedStatisticsTypeException('only tables can be put into PDF');
            }
        }
        else{
            throw new UnsupportedStatisticsTypeException('result does not heave type set');
        }
    }
    
    private static function writeMultipleSheets(ExcelEditor $xlsx, Container $stats) : void {
        $data = $stats->get('data');
        $title = $stats->get('title');
        foreach ($data as $index => $table) {
            $tableData = new Container($table);
            $sheetName = $tableData->get('title');
            if($index === 0) {
                $appconfig = AppConfig::getInstance();
                $cfg = $appconfig->getAppConfig();
                $author = $cfg->get('name') . ' web app';
                $xlsx->newFile($title, $author, $sheetName);
            }
            else{
                $xlsx->addSheet($sheetName);
            }
            self::writeSheet($xlsx, $tableData, $sheetName);
        }
    }
    
    private  static function writeSheet(ExcelEditor $xlsx, Container $stats, string $sheetName) : void {
        self::writeTitle($xlsx, $stats, $sheetName);
        self::writeCells($xlsx, $stats, $sheetName);
    }
    
    private static function writeTitle(ExcelEditor $xlsx, Container $stats, string $sheetName) : void {
        $cells = $stats->get('cells');
        $width = count($cells[0]);
        $titleStart = $xlsx->getAddress(1, 1);
        $titleEnd = $xlsx->getAddress(1, $width);
        $titleRange = $titleStart . ':' . $titleEnd;
        $xlsx->mergeCells($sheetName, $titleStart, $titleEnd);
        $xlsx->writeToCell($sheetName, $titleStart, $stats->get('title'));
        $xlsx->setBorder($sheetName, $titleRange);
        $xlsx->centerCells($sheetName, $titleRange);
        for($col = 1; $col <= $width; $col++) {
            $xlsx->setColumnAutosize($sheetName, $col);
        }
        $xlsx->fillCell($sheetName, $titleRange, 'cccccc');
    }
    
    private static function writeCells(ExcelEditor $xlsx, Container $stats, string $sheetName) : void {
        $cells = $stats->get('cells');
        foreach ($cells as $y => $row) {
            foreach ($row as $x => $value) {
                $address = $xlsx->getAddress($y + 2, $x + 1);
                $xlsx->writeToCell($sheetName, $address, $value);
                $xlsx->setBorder($sheetName, $address);
                $xlsx->centerCells($sheetName, $address);
                if($x === 0 || $y === 0 || ($x === 1 && $cells[0][1] === Group::Users->value)){
                    $xlsx->fillCell($sheetName, $address, 'e6e6e6');
                }
            }
        }
    }
}

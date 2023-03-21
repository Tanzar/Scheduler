<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Engine\File;

use Tanweb\File\ExcelEditor as ExcelEditor;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;

/**
 * Description of TableXLSX
 *
 * @author Tanzar
 */
class TableXLSX {
    
    public static function generate(Container $data) : void {
        $xlsx = new ExcelEditor();
        $name = $data->get('title');
        $author = self::getAuthor();
        $xlsx->newFile($name, $author, $name);
        self::insertData($xlsx, $data);
        $xlsx->sendToBrowser($name);
    }
    
    static private function getAuthor() : string {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $author = $cfg->get('name') . ' web app';
        return $author;
    }
    
    private static function insertData(ExcelEditor $xlsx, Container $data) : void {
        $cells = $data->get('cells');
        $sheet = $xlsx->getCurrentSheetName();
        foreach ($cells as $y => $row) {
            foreach ($row as $x => $value) {
                self::manageCell($xlsx, $sheet, $x, $y, $value);
            }
        }
    }
    
    private static function manageCell(ExcelEditor $xlsx, string $sheet, int $x, int $y, string $value) : void {
        $xlsx->setColumnAutosize($sheet, $x + 1);
        $address = $xlsx->getAddress($y + 1, $x + 1);
        $xlsx->writeToCell($sheet, $address, $value);
        $xlsx->setBorder($sheet, $address);
        if($x === 0 || $y === 0){
            $xlsx->fillCell($sheet, $address, 'c7c7c7');
        }
        if($y === 0 || $x !== 0){
            $xlsx->centerCells($sheet, $address);
        }
    }
}

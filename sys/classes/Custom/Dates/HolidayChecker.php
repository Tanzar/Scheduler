<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Dates;

use Tanweb\Config\INI\AppConfig as AppConfig;
use Tanweb\Container as Container;
use DateTime;
/**
 * Description of HolidayChecker
 *
 * @author Tanzar
 */
class HolidayChecker {
    
    public static function isHoliday(DateTime $date) : bool {
        $dateString = $date->format('Y-m-d');
        $year = (int) $date->format('Y');
        $holidays = self::getHolidaysList($year);
        foreach ($holidays as $holiday) {
            if($holiday === $dateString){
                return true;
            }
        }
        return false;
    }
    
    private static function getHolidaysList(int $year) : array {
        $appconfig = AppConfig::getInstance();
        $config = $appconfig->getAppConfig();
        $holidays = $config->get('holiday_static');
        foreach ($holidays as $index => $value) {
            $holidays[$index] = $year . $value;
        }
        $easterMondayDate = self::getEasterMondayDate($year);
        $holidays[] = $easterMondayDate;
        $holidays[] = self::getHolySpiritDay($easterMondayDate);
        $holidays[] = self::getBozeCialoDate($easterMondayDate);
        return $holidays;
    }
    
    private static function getEasterMondayDate(int $year) : string {
        $date = new DateTime(date("Y-m-d", easter_date($year)));
        $date->modify('+2 days');
        return $date->format('Y-m-d');
    }
    
    private static function getHolySpiritDay(string $easterMondayDate) : string {
        $date = new DateTime($easterMondayDate);
        $date->modify('+48 days');
        return $date->format('Y-m-d');
    }
    
    private static function getBozeCialoDate(string $easterMondayDate) : string {
        //nie wiem jak nazwać po angielsku :P
        $date = new DateTime($easterMondayDate);
        $date->modify('+59 days');
        return $date->format('Y-m-d');
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Reports;

use Custom\Statistics\SingleStats as SingleStats;
use Tanweb\Container;
use Tanweb\Config\INI\Languages as Languages;
/**
 * Description of ScheduleReport
 *
 * @author Tanzar
 */
class ScheduleReport {
    
    public static function generate(int $year, string $username) : Container {
        $languages = Languages::getInstance();
        $modules = new Container($languages->get('modules'));
        $settings = array(
            "name" => $modules->get('schedule'),
            "type" => "Pojedyncze",
            "json" => '{"x": "Miesiące", "y": "Czynności", "inputs": ["Rok", "Użytkownik"], "method": "Zliczanie roboczodniówek", "dataset": "Wpisy harmonogramu", "resultForm": "Tabela"}'
        );
        $data = new Container($settings);
        $inputsValues = new Container();
        $inputsValues->add($year, 'year');
        $inputsValues->add($username, 'user');
        $stats = new SingleStats($data, $inputsValues);
        return $stats->generate();
    }
}

<?php

/*
 * This code is free to use, just remember to give credit.
 */
namespace Custom\Reports;

use Custom\Statistics\Engine\Stats as Stats;
use Tanweb\Container;
use Tanweb\Config\INI\Languages as Languages;
/**
 * Description of ScheduleReport
 *
 * @author Tanzar
 */
class ScheduleReport {
    
    public static function generate(int $year, string $username) : string {
        $languages = Languages::getInstance();
        $modules = new Container($languages->get('modules'));
        $name = $modules->get('schedule');
        $config = array(
            'title' => $name,
            'inputs' => array(
                array('type' => 'Rok', 'value' => '' . $year),
                array('type' => 'Użytkownik', 'value' => $username)
            ), 
            'datasets' => array(
                array(
                    'index' => 'Wpisy',
                    'groups' => array('Miesiące', 'Rodzaj Czynności'),
                    'dataset' => 'Wpisy na harmonogramie',
                    'operation' => 'Zliczanie roboczodniówek'
                )
            ), 
            'output_form' => 'Tabela',
            'output_config' => array(
                'cols' => 'Miesiące',
                'rows' => 'Rodzaj Czynności',
                'dataset' => 'Wpisy'
            )
        );
        $inputs = new Container();
        $stats = Stats::generateOutput(new Container($config), $inputs);
        return $stats->get('HTML');
    }
}

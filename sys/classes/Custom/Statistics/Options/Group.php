<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Tanweb\Database\Database as Database;
use Tanweb\Config\INI\Languages as Languages;
use Tanweb\Config\INI\AppConfig as AppConfig;
use Custom\Statistics\Options\DataSet as DataSet;
use DateTime;

/**
 *
 * @author Tanzar
 */
enum Group : string {
    case Days = 'Dni';
    case Months = 'Miesiące';
    case Years = 'Lata';
    case Users = 'Użytkownicy';
    case Activities = 'Czynności';
    case InspectionActivities = 'Czynności kontrolne';
    case Locations = 'Miejsca';
    case LocationGroups = 'Grupy miejsc';
    case LocationTypes = 'Typy miejsc';
    
    public function getOptions(Container $inputValues) : Container {
        return match($this) {
            Group::Days => $this->getDayNumbers($inputValues),
            Group::Months => $this->getMonths($inputValues),
            Group::Years => $this->getYears($inputValues),
            Group::Users => $this->getUsers($inputValues),
            Group::Activities => $this->getActivities(),
            Group::InspectionActivities => $this->getInspectionActivities(),
            Group::Locations => $this->getInspectionLocations($inputValues),
            Group::LocationGroups => $this->getLocationGroups($inputValues),
            Group::LocationTypes => $this->getInspectionLocationTypes($inputValues)
        };
    }
    
    private function getDayNumbers(Container $inputValues) : Container {
        $start = new DateTime(date('Y-m') . '-1');
        $end = new DateTime(date('Y-m') . '-' . $start->format('t'));
        if($inputValues->isValueSet('month') && $inputValues->isValueSet('year')){
            $start = new DateTime($inputValues->get('year') . '-' . $inputValues->get('month') . '-1');
            $end = new DateTime($inputValues->get('year') . '-' . $inputValues->get('month') . '-' . $start->format('t'));
        }
        elseif ($inputValues->isValueSet('month')) {
            $start = new DateTime(date('Y') . '-' . $inputValues->get('month') . '-1');
            $end = new DateTime(date('Y') . '-' . $inputValues->get('month') . '-' . $start->format('t'));
        }
        elseif ($inputValues->isValueSet('year')) {
            $start = new DateTime($inputValues->get('year') . '-' . date('m') . '-1');
            $end = new DateTime($inputValues->get('year') . '-' . date('m') . '-' . $start->format('t'));
        }
        $result = new Container();
        while($start <= $end){
            $result->add(array(
                'title' => $start->format('j'),
                'value' => $start->format('j')
            ));
            $start->modify('+1 days');
        }
        return $result;
    }
    
    private function getMonths(Container $inputValues) : Container {
        $languages = Languages::getInstance('polski');
        $months = $languages->get('months');
        $result = new Container();
        if ($inputValues->isValueSet('month')) {
            $month = $inputValues->get('month');
            $result->add(array(
                'title' => $months[$month],
                'value' => $month
            ));
        }
        else{
            foreach ($months as $number => $text) {
                $result->add(array(
                    'title' => $text,
                    'value' => $number
                ));
            }
        }
        return $result;
    }
    
    private function getYears(Container $inputValues) : Container {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $startYear = $cfg->get('yearStart');
        $result = new Container();
        if ($inputValues->isValueSet('year')) {
            $year = $inputValues->get('year');
            $result->add(array(
                'title' => $year,
                'value' => $year
            ));
        }
        else{
            for($year = $startYear; $year <= (int) date('Y'); $year++){
                $result->add(array(
                    'title' => $year,
                    'value' => $year
                ));
            }
        }
        return $result;
    }
    
    private function getUsers(Container $inputValues) : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('suzug_user_details')->where('active', 1)
                ->orderBy('year')->orderBy('number');
        if ($inputValues->isValueSet('year')) {
            $sql->and()->where('year', $inputValues->get('year'));
        }
        else{
            $sql->and()->where('year', date('Y'));
        }
        if ($inputValues->isValueSet('username')) {
            $sql->and()->where('username', $inputValues->get('username'));
        }
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name') . ' ' . $element->get('surname'),
                'value' => $element->get('username'),
                'SUZUG' => $element->get('number'),
                'year' => $element->get('year')
            ));
        }
        return $result;
    }
    
    private function getActivities() : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('activity');
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    private function getInspectionActivities() : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('activity')->where('require_document', 1);
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    private function getInspectionLocations(Container $inputValues) : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('inspection', 1);
        if ($inputValues->isValueSet('id_location')) {
            $sql->and()->where('id', $inputValues->get('id_location'));
        }
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    private function getLocationGroups(Container $inputValues) : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('location_groups');
        $data = $database->select($sql);
        if ($inputValues->isValueSet('id_location_group')) {
            $sql->and()->where('id', $inputValues->get('id_location_group'));
        }
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    private function getInspectionLocationTypes(Container $inputValues) : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('location_types')->where('inspection', 1);
        if ($inputValues->isValueSet('id_location_type')) {
            $sql->and()->where('id', $inputValues->get('id_location_type'));
        }
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    public static function getGroupsForDataSet(DataSet $dataset) : Container {
        return match ($dataset){
            DataSet::Articles => self::formContainer(Group::Days),
            DataSet::CourtApplications => self::formContainer(Group::Days),
            DataSet::Decisions => self::formContainer(Group::Days),
            DataSet::Entries => self::formContainer(Group::Activities, Group::InspectionActivities),
            DataSet::Inspections => self::formContainer(Group::Activities, Group::InspectionActivities),
            DataSet::InstrumentUsages => self::formContainer(Group::Days),
            DataSet::Suspensions => self::formContainer(Group::Days),
            DataSet::SuspensionsArticles => self::formContainer(Group::Days),
            DataSet::SuspensionsDecisions => self::formContainer(Group::Days),
            DataSet::SuspensionsTickets => self::formContainer(Group::Days),
            DataSet::Tickets => self::formContainer(Group::Days)
        };
    }
    
    private static function formContainer(Group ...$groups) : Container {
        $result = new Container();
        $result->add(Group::Months->value);
        $result->add(Group::Years->value);
        $result->add(Group::Users->value);
        $result->add(Group::Locations->value);
        $result->add(Group::LocationGroups->value);
        $result->add(Group::LocationTypes->value);
        foreach ($groups as $group) {
            $result->add($group->value);
        }
        return $result;
    }
    
    public function getColumn() : string {
        return match($this) {
            Group::Days => 'day',
            Group::Months => 'month',
            Group::Years => 'year',
            Group::Users => 'username',
            Group::Activities => 'is_activity',
            Group::InspectionActivities => 'id_activity',
            Group::Locations => 'id_location',
            Group::LocationGroups => 'id_location_group',
            Group::LocationTypes => 'id_location_type'
        };
    }
    
    public function getValue(Container $dataRow) : string {
        return match($this) {
            Group::Days => $this->getDay($dataRow),
            Group::Months => $this->getMonth($dataRow),
            Group::Years => $this->getYear($dataRow),
            Group::Users => $dataRow->get('username'),
            Group::Activities => $dataRow->get('id_activity'),
            Group::InspectionActivities => $dataRow->get('id_activity'),
            Group::Locations => $dataRow->get('id_location'),
            Group::LocationGroups => $dataRow->get('id_location_group'),
            Group::LocationTypes => $dataRow->get('id_location_type')
        };
    }
    
    private function getDay(Container $dataRow) : string {
        $dateString = $dataRow->get('date');
        $date = new DateTime($dateString);
        return $date->format('d');
    }
    
    private function getMonth(Container $dataRow){
        $dateString = $dataRow->get('date');
        $date = new DateTime($dateString);
        return $date->format('n');
    }
    
    private function getYear(Container $dataRow){
        $dateString = $dataRow->get('date');
        $date = new DateTime($dateString);
        return $date->format('Y');
    }
    
    
}

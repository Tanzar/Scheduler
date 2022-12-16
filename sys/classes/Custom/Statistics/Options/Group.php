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
    case Quarters = 'Kwartały';
    case Users = 'Użytkownicy';
    case UserTypes = 'Typy Użytkowników';
    case Activities = 'Czynności';
    case InspectionActivities = 'Czynności kontrolne';
    case Locations = 'Miejsca';
    case LocationGroups = 'Grupy miejsc';
    case LocationTypes = 'Typy miejsc';
    case SuspensionType = 'Typy zatrzymań';
    case Ground = 'Poziom';
    
    public function getOptions(Container $inputValues = new Container()) : Container {
        return match($this) {
            Group::Days => $this->getDayNumbers($inputValues),
            Group::Months => $this->getMonths($inputValues),
            Group::Years => $this->getYears($inputValues),
            Group::Quarters => $this->getQuarters(),
            Group::Users => $this->getUsers($inputValues),
            Group::UserTypes => $this->getUserTypes(),
            Group::Activities => $this->getActivities(),
            Group::InspectionActivities => $this->getInspectionActivities(),
            Group::Locations => $this->getInspectionLocations($inputValues),
            Group::LocationGroups => $this->getLocationGroups($inputValues),
            Group::LocationTypes => $this->getInspectionLocationTypes($inputValues),
            Group::SuspensionType => $this->getSuspensionTypes(),
            Group::Ground => $this->getGrounds()
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
    
    private function getQuarters() : Container {
        $result = new Container();
        $result->add(array(
            'title' => 'I Kwartał',
            'value' => 1
        ));
        $result->add(array(
            'title' => 'II Kwartał',
            'value' => 2
        ));
        $result->add(array(
            'title' => 'III Kwartał',
            'value' => 3
        ));
        $result->add(array(
            'title' => 'IV Kwartał',
            'value' => 4
        ));
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
                'year' => $element->get('year'),
                'user_type' => $element->get('user_type')
            ));
        }
        return $result;
    }
    
    private function getUserTypes() : Container {
        $appconfig = AppConfig::getInstance();
        $cfg = $appconfig->getAppConfig();
        $types = $cfg->get('user_type_inspector');
        $result = new Container();
        foreach ($types as $type) {
            $result->add(array(
                'title' => $type,
                'value' => $type
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
        $sql = $this->formLocationSQL($inputValues);
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
    
    private function formLocationSQL(Container $inputValues) : MysqlBuilder {
        $sql = new MysqlBuilder();
        $sql->select('location_details')->where('inspection', 1);
        if ($inputValues->isValueSet('id_location')) {
            $sql->and()->where('id', $inputValues->get('id_location'));
        }
        if ($inputValues->isValueSet('month') && $inputValues->isValueSet('year')) {
            $start = new DateTime($inputValues->get('year') . '-' . $inputValues->get('month') . '-01');
            $end = new DateTime($start->format('Y-m-t'));
            $sql->and()->openBracket()->openBracket()
                    ->where('active_from', $start->format('Y-m-d'), '<=')
                    ->and()->where('active_to', $start->format('Y-m-d'), '>=')
                    ->closeBracket()->or()->openBracket()
                    ->where('active_from', $end->format('Y-m-d'), '<=')
                    ->and()->where('active_to', $end->format('Y-m-d'), '>=')
                    ->closeBracket()->closeBracket();
        }
        elseif ($inputValues->isValueSet('year')) {
            $sql->and()->where('year(active_from)', $inputValues->get('year'), '<=')
                    ->and()->where('year(active_to)', $inputValues->get('year'), '>=');
        }
        return $sql;
    }
    
    private function getLocationGroups(Container $inputValues) : Container {
        $database = Database::getInstance('scheduler');
        $sql = $this->formLocationGroupSQL($inputValues);
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
    
    private function formLocationGroupSQL(Container $inputValues) : MysqlBuilder {
        $sql = new MysqlBuilder();
        $sql->select('location_group')->where('inspection', 1);
        if ($inputValues->isValueSet('id_location_group')) {
            $sql->and()->where('id', $inputValues->get('id_location_group'));
        }
        if ($inputValues->isValueSet('month') && $inputValues->isValueSet('year')) {
            $start = new DateTime($inputValues->get('year') . '-' . $inputValues->get('month') . '-01');
            $end = new DateTime($start->format('Y-m-t'));
            $sql->and()->openBracket()->openBracket()
                    ->where('active_from', $start->format('Y-m-d'), '<=')
                    ->and()->where('active_to', $start->format('Y-m-d'), '>=')
                    ->closeBracket()->or()->openBracket()
                    ->where('active_from', $end->format('Y-m-d'), '<=')
                    ->and()->where('active_to', $end->format('Y-m-d'), '>=')
                    ->closeBracket()->closeBracket();
        }
        elseif ($inputValues->isValueSet('year')) {
            $sql->and()->where('year(active_from)', $inputValues->get('year'), '<=')
                    ->and()->where('year(active_to)', $inputValues->get('year'), '>=');
        }
        return $sql;
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
    
    private function getSuspensionTypes() : Container {
        $database = Database::getInstance('scheduler');
        $sql = new MysqlBuilder();
        $sql->select('suspension_type_details');
        $data = $database->select($sql);
        $result = new Container();
        foreach ($data->toArray() as $item) {
            $element = new Container($item);
            $result->add(array(
                'title' => $element->get('group_name') . ' - ' . $element->get('name'),
                'value' => $element->get('id')
            ));
        }
        return $result;
    }
    
    private function getGrounds() : Container {
        $result = new Container();
        $result->add(array(
            'title' => 'Dół',
            'value' => 1
        ));
        $result->add(array(
            'title' => 'Góra',
            'value' => 0
        ));
        return $result;
    }
    
    public static function getGroupsForDataSet(DataSet $dataset) : Container {
        return match ($dataset){
            DataSet::Articles => self::formContainer(),
            DataSet::CourtApplications => self::formContainer(),
            DataSet::Decisions => self::formContainer(),
            DataSet::Entries => self::formContainer(Group::Activities, Group::InspectionActivities, Group::Ground),
            DataSet::Inspections => self::formContainer(Group::Activities, Group::InspectionActivities, Group::Ground),
            DataSet::InstrumentUsages => self::formContainer(),
            DataSet::Suspensions => self::formContainer(Group::SuspensionType),
            Dataset::SuspensionsWithDecisions => self::formContainer(Group::SuspensionType),
            Dataset::SuspensionsWithoutDecisions => self::formContainer(Group::SuspensionType),
            DataSet::SuspensionsArticles => self::formContainer(),
            DataSet::SuspensionsDecisions => self::formContainer(),
            DataSet::SuspensionsTickets => self::formContainer(),
            DataSet::Tickets => self::formContainer()
        };
    }
    
    private static function formContainer(Group ...$groups) : Container {
        $result = new Container();
        $result->add(Group::Days->value);
        $result->add(Group::Months->value);
        $result->add(Group::Years->value);
        $result->add(Group::Quarters->value);
        $result->add(Group::Users->value);
        $result->add(Group::UserTypes->value);
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
            Group::Quarters => 'quarter',
            Group::Users => 'username',
            Group::UserTypes => 'user_type',
            Group::Activities => 'id_activity',
            Group::InspectionActivities => 'id_activity',
            Group::Locations => 'id_location',
            Group::LocationGroups => 'id_location_group',
            Group::LocationTypes => 'id_location_type',
            Group::SuspensionType => 'id_suspension_type',
            Group::Ground => 'underground'
        };
    }
    
    public function getValue(Container $dataRow) : string {
        return match($this) {
            Group::Days => $this->getDay($dataRow),
            Group::Months => $this->getMonth($dataRow),
            Group::Years => $this->getYear($dataRow),
            Group::Quarters => $this->getQuarter($dataRow),
            Group::Users => $dataRow->get('username'),
            Group::UserTypes => $dataRow->get('user_type'),
            Group::Activities => $dataRow->get('id_activity'),
            Group::InspectionActivities => $dataRow->get('id_activity'),
            Group::Locations => $dataRow->get('id_location'),
            Group::LocationGroups => $dataRow->get('id_location_group'),
            Group::LocationTypes => $dataRow->get('id_location_type'),
            Group::SuspensionType => $dataRow->get('id_suspension_type'),
            Group::Ground => $dataRow->get('underground')
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
    
    private function getQuarter(Container $dataRow){
        if($dataRow->isValueSet('date')){
            $dateString = $dataRow->get('date');
        }
        else{
            $dateString = $dataRow->get('year') . '-' . $dataRow->get('month') .
                    '-' . $dataRow->get('day');
        }
        $date = new DateTime($dateString);
        $month = (int) $date->format('n');
        return ceil($month / 3);
    }
}

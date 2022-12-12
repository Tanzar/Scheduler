<?php

/*
 * This code is free to use, just remember to give credit.
 */

namespace Custom\Statistics\Options;

use Tanweb\Container as Container;
use Tanweb\Database\SQL\MysqlBuilder as MysqlBuilder;
use Custom\Statistics\Options\Input as Input;
use Tanweb\Database\Database as Database;

/**
 *
 * @author Tanzar
 */
enum DataSet : string {
    case Entries = 'Wpisy harmonogramu';
    case Inspections = 'Wpisy na kontrolach';
    case Tickets = 'Mandaty';
    case Articles = 'Art 41';
    case Decisions = 'Decyzje';
    case Suspensions = 'Zatrzymania';
    case SuspensionsWithDecisions = 'Zatrzymania z decyzjami';
    case SuspensionsWithoutDecisions = 'Zatrzymania bez decyzji';
    case SuspensionsTickets = 'Mandaty dla zatrzymań';
    case SuspensionsArticles = 'Art 41 dla zatrzymań';
    case SuspensionsDecisions = 'Decyzje dla zatrzymań';
    case InstrumentUsages = 'Wykorzystania przyrządów';
    case CourtApplications = 'Wnioski do sądu';
    
    public function getData(Container $inputsValues = new Container()) : Container {
        $database = Database::getInstance('scheduler');
        $sql = $this->getSQL($inputsValues);
        return $database->select($sql);
    }
    
    private function getSQL(Container $inputsValues) : MysqlBuilder {
        return match($this) {
            Dataset::Entries => $this->formEntrySQL($inputsValues),
            Dataset::Inspections => $this->formEntrySQL($inputsValues),
            Dataset::Tickets => $this->formSQL($inputsValues),
            Dataset::Articles => $this->formSQL($inputsValues),
            Dataset::Decisions => $this->formSQL($inputsValues),
            Dataset::Suspensions => $this->formSQL($inputsValues),
            Dataset::SuspensionsWithDecisions => $this->formSQL($inputsValues),
            Dataset::SuspensionsWithoutDecisions => $this->formSQL($inputsValues),
            Dataset::SuspensionsTickets => $this->formSQL($inputsValues),
            Dataset::SuspensionsArticles => $this->formSQL($inputsValues),
            Dataset::SuspensionsDecisions => $this->formSQL($inputsValues),
            Dataset::InstrumentUsages => $this->formSQL($inputsValues),
            Dataset::CourtApplications => $this->formSQL($inputsValues),
        };
    }
    
    private function formSQL(Container $inputsValues) : MysqlBuilder {
        $view = $this->getViewName();
        $sql = new MysqlBuilder();
        $sql->select($view)->where('active', 1);
        foreach ($inputsValues->toArray() as $key => $value) {
            $sql->and();
            $column = Input::getColumnByVariableName($key);
            if($key === 'yearStart' || $key === 'monthStart'){
                $sql->where($column, $value, '>=');
            }
            elseif($key === 'yearEnd' || $key === 'monthEnd'){
                $sql->where($column, $value, '<=');
            }
            else{
                $sql->where($column, $value);
            }
        }
        return $sql;
    }
    
    private function formEntrySQL(Container $inputsValues) : MysqlBuilder {
        $view = $this->getViewName();
        $sql = new MysqlBuilder();
        $sql->select($view)->where('active', 1);
        $this->addEntryDatesConditions($sql, $inputsValues);
        $ignoreKeys = new Container(['year', 'month', 'monthStart', 'monthEnd', 'yearStart', 'yearEnd']);
        foreach ($inputsValues->toArray() as $key => $value) {
            if(!$ignoreKeys->contains($key)){
                $sql->and();
                $column = Input::getColumnByVariableName($key);
                $sql->where($column, $value);
            }
        }
        return $sql;
    }
    
    private function addEntryDatesConditions(MysqlBuilder $sql, Container $inputValues) : void {
        if($inputValues->isValueSet('month') && $inputValues->isValueSet('year')){
            $this->addMonthAndYear($sql, $inputValues);
        }
        elseIf($inputValues->isValueSet('month')){
            $this->addMonth($sql, $inputValues);
        }
        elseif($inputValues->isValueSet('year')){
            $this->addYear($sql, $inputValues);
        }
        if($inputValues->isValueSet('monthStart') && $inputValues->isValueSet('monthEnd')){
            $this->addMonthsRange($sql, $inputValues);
        }
        if($inputValues->isValueSet('YearStart') && $inputValues->isValueSet('yearEnd')){
            $this->addYearsRange($sql, $inputValues);
        }
    }
    
    private function addMonthAndYear(MysqlBuilder $sql, Container $inputValues) : void {
        $month = $inputValues->get('month');
        $year = $inputValues->get('year');
        $sql->and()->openBracket()->openBracket()->
                where('month(start)', $month)->and()->where('year(start)', $year)
                ->closeBracket()->or()->openBracket()->
                where('month(end)', $month)->and()->where('year(end)', $year)
                ->closeBracket()->closeBracket();
    }
    
    private function addMonth(MysqlBuilder $sql, Container $inputValues) : void {
        $month = $inputValues->get('month');
        $sql->and()->openBracket()->
                where('month(start)', $month)->or()->where('month(end)', $month)
                ->closeBracket();
    }
    
    private function addYear(MysqlBuilder $sql, Container $inputValues) : void {
        $year = $inputValues->get('year');
        $sql->and()->openBracket()->
                where('year(start)', $year)->or()->where('year(end)', $year)
                ->closeBracket();
    }
    
    private function addMonthsRange(MysqlBuilder $sql, Container $inputValues) : void {
        $start = $inputValues->get('monthStart');
        $end = $inputValues->get('monthEnd');
        $sql->and()->openBracket()->openBracket()->
                where('month(start)', $start, '>=')->and()->where('month(start)', $end, '<=')
                ->closeBracket()->or()->openBracket()->
                where('month(end)', $start, '>=')->and()->where('month(end)', $end, '<=')
                ->closeBracket()->closeBracket();
    }
    
    private function addYearsRange(MysqlBuilder $sql, Container $inputValues) : void {
        $start = $inputValues->get('yearStart');
        $end = $inputValues->get('yearEnd');
        $sql->and()->openBracket()->openBracket()->
                where('year(start)', $start, '>=')->and()->where('year(start)', $end, '<=')
                ->closeBracket()->or()->openBracket()->
                where('year(end)', $start, '>=')->and()->where('year(end)', $end, '<=')
                ->closeBracket()->closeBracket();
    }
    
    public function getViewName() : string {
        return match($this) {
            Dataset::Entries => 'statistics_schedule_entries',
            Dataset::Inspections => 'statistics_document_entries',
            Dataset::Tickets => 'suzug_ticket',
            Dataset::Articles => 'suzug_art_41',
            Dataset::Decisions => 'suzug_decision',
            Dataset::Suspensions => 'suzug_suspension',
            Dataset::SuspensionsTickets => 'suzug_suspension_ticket',
            Dataset::SuspensionsArticles => 'suzug_suspension_art_41',
            Dataset::SuspensionsWithDecisions => 'suzug_suspension_with_decision',
            Dataset::SuspensionsWithoutDecisions => 'suzug_suspension_without_decision',
            Dataset::SuspensionsDecisions => 'suzug_suspension_decision',
            Dataset::InstrumentUsages => 'suzug_instrument_usage',
            Dataset::CourtApplications => 'suzug_court_application'
        };
    }
}
